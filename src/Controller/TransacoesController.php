<?php

namespace App\Controller;

use App\Dto\TransacaoRealizarDto;
use App\Repository\ContaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacoesController extends AbstractController
{
    #[Route('/transacoes', name: 'transacoes_realizar', methods: ['POST'])]

    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,
        ContaRepository $contaRepository
    ): JsonResponse
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
            $this->json([
                'message'=>'Conta de origem não encontrada!'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if(!$contaDestino){
            return $this->json([
                 'message'=>'Conta de destino não encontrada!'
            ], 404);
        }
        
        



        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TransacoesController.php',
        ]);
    }
}
