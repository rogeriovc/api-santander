<?php

namespace App\Controller;

use App\Dto\TransacaoRealizarDto;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacoesController extends AbstractController
{
    #[Route('/transacoes', name: 'transacoes_realizar', methods: ['POST'])]

    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,
        ContaRepository $contaRepository,
        EntityManagerInterface $entityManager
    ):  Response
    {
        $erros = [];
        
        if (!$entrada->getIdUsuarioOrigem()){
            array_push($erros, [
                'message'=> 'Conta de origem é obrigatoria'
            ]);
        }

        if (!$entrada->getIdUsuarioDestino()){
            array_push($erros, [
                'message'=> 'Conta de destino é obrigatoria'
            ]);
        }

        if (!$entrada->getValor()){
            array_push($erros, [
                'message'=> 'Valor é obrigatorio'
            ]);
        }

        if ((float)$entrada->getValor()<=0){
            array_push($erros, [
                'message'=> 'Valor deve ser maior que zero'
            ]);
        }

        if ($entrada->getIdUsuarioOrigem() === $entrada->getIdUsuarioDestino()){
            array_push($erros, [
                'message'=> 'As contas devem ser distintas'
            ]);
        }
        
        $contaOrigem = $contaRepository->findByUsuarioId($entrada->getIdUsuarioOrigem());
        if (!$contaOrigem){
            return $this->json([
                'message'=>'Conta de origem não encontrada!'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if(!$contaDestino){
            return $this->json([
                 'message'=>'Conta de destino não encontrada!'
            ], 404);
        }

        if ((float)$contaOrigem->getSaldo() < (float) $entrada->getValor()) {
            return $this->json([
                'message' => 'Saldo insuficiente'
            ], 400);
        }

        //realizar  a transaçao e salvar no banco

        $saldo = (float) $contaOrigem->getSaldo();
        $valorT = (float) $entrada-> getValor();
        $saldoDestino = (float) $contaDestino->getSaldo();

        $contaOrigem->setSaldo($saldo - $valorT);
        $entityManager->persist(($contaOrigem));
        $contaDestino->setSaldo(($valorT + $saldoDestino));
        $entityManager->persist(($contaDestino));

        $transacao = new Transacao();

        $transacao->setDataHora(new DateTime());
        $transacao->setValor($entrada->getValor());
        $transacao->setContaOrigem($contaOrigem);
        $transacao->setContaDestino($contaDestino);
        $entityManager->persist($transacao);

        $entityManager->flush();
        return new Response(status:204);



        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TransacoesController.php',
        ]);
    }
}
