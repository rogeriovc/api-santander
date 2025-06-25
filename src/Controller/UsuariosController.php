<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name:'usuarios_criar', methods:['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat:'json')]
        UsuarioDto $usuarioDto,

        EntityManagerInterface $entityManager
    ): JsonResponse
    {
       $erros = [];
        if (!$usuarioDto->getCpf()){
            array_push($erros, [
                'message'=> 'Cpf é obrigatorio!'
            ]);
       }

       if (empty($usuarioDto->getNome())){
            array_push($erros, [
                'message'=> 'Nome é obrigatorio!'
            ]);
       }
        if (empty($usuarioDto->getEmail())){
            array_push($erros, [
                'message'=> 'E-mail é obrigatorio!'
        ]   );
       }
        if (!$usuarioDto->getSenha()){
            array_push($erros, [
                'message'=> 'Senha é obrigatorio!'
            ]);
       }

         if (empty($usuarioDto->getTelefone())){
            array_push($erros, [
                'message'=> 'Telefone é obrigatorio!'
            ]);
        }
        if(count($erros)>0) {
            return $this->json($erros, 422);
        }  

        $usuario = new Usuario();
        $usuario-> setCpf($usuarioDto->getCpf());
        $usuario-> setNome($usuarioDto->getNome());
        $usuario-> setEmail($usuarioDto->getEmail());
        $usuario-> setSenha($usuarioDto->getSenha());
        $usuario-> setTelefone($usuarioDto->getTelefone());

            $entityManager->persist($usuario);
            $entityManager->flush();



         return $this->json($erros);
        

    }
}
