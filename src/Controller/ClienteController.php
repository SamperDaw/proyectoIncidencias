<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Cliente;
use App\Entity\Incidencia;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class ClienteController extends AbstractController
{
    /**
     * @Route("/cliente", name="listado_clientes")
     */
    public function index(ManagerRegistry $doctrine): Response{
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        $repositorio = $doctrine->getRepository(Cliente::class);
        $cliente = $repositorio->findAll();
               
        return $this->render('cliente/index.html.twig', [
            'clientes' =>$cliente,
        ]);
    }
    
    
    /**
     *    Inserta un post utilizando formulario de symfoni
     * @Route ("/cliente/insertarCliente", name="insertar_cliente")
     */
     public function insertarCliente(Request $request, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
         $cliente = new Cliente();

         $form = $this->createFormBuilder($cliente)
                ->add('nombre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, indique el nombre del cliente.',
                    ]),
                ],
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Ingrese Nombre ',
                    
                )
            ])
                ->add('apellidos',TextType::class, [
                    'label' => false,
                    'attr' => array(
                    'placeholder' => 'Ingrese Apellidos '
                    
                )
                ] )
                ->add('telefono', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, indique el teléfono del cliente.',
                    ]),
                ],
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Ingrese Teléfono '
                    
                )
            ])
                ->add('direccion',TextType::class, [
                    'label' => false,
                    'attr' => array(
                    'placeholder' => 'Ingrese Dirección '
                    
                )
                ])
                ->add('Insertar', SubmitType::class, array(
                    'label' => 'Insertar Cliente'
                   
                ))
                ->getForm();
                ;
        $form->handleRequest($request);
        
        
        if($form->isSubmitted() && $form->isValid()){
            $nombre = $form->get('nombre')->getData();
            $apellidos = $form->get('apellidos')->getData();
            $telefono = $form->get('telefono')->getData();
            $direccion = $form->get('direccion')->getData();
            $cliente->setNombre($nombre);
            $cliente->setApellidos($apellidos);
            $cliente->setTelefono($telefono);
            $cliente->setDireccion($direccion);
            $em = $doctrine->getManager();
            $em->persist($cliente);
            $em->flush();
            
            $this->addFlash("aviso", "Cliente Insertado");
            return $this->redirectToRoute("listado_clientes");
        }
        return $this->renderForm('cliente/insertarCliente.html.twig', ['form_cliente'=>$form]);
    }
        
    
    
    /**
     * @Route("/cliente/borrar/{id<\d+>}",name="borrar_cliente")
     */
    public function borrar(Cliente $cliente, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_REMEMBERED')) {
            $this->addFlash("aviso", "Vuelve a iniciar sesión para eliminar un cliente.");
            return $this->redirectToRoute('listado_clientes');
        }
        
        $em = $doctrine->getManager();
        $em->remove($cliente);
        $em->flush();

        $this->addFlash("aviso", "Mensaje borrado");
        return $this->redirectToRoute("listado_clientes");
    }
    
    /**
     * @Route("/cliente/{id<\d+>}",name="ver_cliente")
     */
    public function ver(Cliente $verCli, Request $request, ManagerRegistry $doctrine):Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        
        
        $incidencias = new Incidencia(); 
        $repositorio = $doctrine->getRepository(Cliente::class);
        $repositorio2 = $doctrine->getRepository(Incidencia::class);
        $id = $request->get('id');
        $cliente = $repositorio->find($id);
        $incidencias = $repositorio2->findByIdCliente($id);
        
        return $this->render("cliente/ver.html.twig", [
            "cliente" => $cliente,
            "incidenciaCli"=>$incidencias]);
                
    }
}
