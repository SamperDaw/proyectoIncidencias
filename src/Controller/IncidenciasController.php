<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Incidencia;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Cliente;
use App\Entity\Usuario;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class IncidenciasController extends AbstractController {

    #[Route('/incidencias', name: 'app_incidencias')]
    public function index(ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $repositorio = $doctrine->getRepository(Incidencia::class);
        $incidencias = $repositorio->findBy(
                [],
                ["fechaCreacion" => "DESC"]
        );

        return $this->render('incidencias/listaIncidencias.html.twig', [
                    'controller_name' => 'Incidencias',
                    'incidenciaslista' => $incidencias
        ]);
    }

    /**
     * @Route("/incidencia/insertar/{id<\d+>}", name="insertar_incidencia")
     */
    public function insertarIncidencia(Cliente $cliente, Request $request, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidencia)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El titulo es obligatorio',
                                ]),
                    ],
                    'attr' => array(
                        'placeholder' => 'Titulo...',
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ],
                ])
                ->add('Insertar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();

            $incidencia->setTitulo($titulo);
            $incidencia->setEstado($estado);
            $incidencia->setCliente($cliente);
            $incidencia->setUsuario($this->getUser());
            $incidencia->setFechaCreacion(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($incidencia);
            $em->flush();
            return $this->redirectToRoute('listado_clientes');
        }
        return $this->renderForm('incidencias/insertarIncidencia.html.twig', ['form_cliente' => $form]);
    }

    /**
     * @Route("/incidencia/insertar2", name="insertar_incidencia2")
     */
    public function insertarIncidencia2(Request $request, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidencia)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El titulo es obligatorio',
                                ]),
                    ],
                    'attr' => array(
                        'placeholder' => 'Titulo...',
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ],
                ])
                ->add('Cliente', EntityType::class, [
                    'class' => Cliente::class,
                    'choice_label' => 'nombre',
                    'choice_value' => 'id',
                    'attr' => array(
                        'class' => 'controls'
                    )
                ])
                ->add('Insertar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();
            $cliente = $form->get('Cliente')->getData();
            $incidencia->setTitulo($titulo);
            $incidencia->setEstado($estado);
            $incidencia->setCliente($cliente);
            $incidencia->setUsuario($this->getUser());
            $incidencia->setFechaCreacion(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($incidencia);
            $em->flush();
            return $this->redirectToRoute('listado_clientes');
        }
        return $this->renderForm('incidencias/insertarIncidenciaDesdeIncidencia.html.twig', ['form_incidencias' => $form]);
    }

    /**
     * @Route("/incidencia/borrar/{id<\d+>}",name="borrar_incidencia")
     */
    public function borrar(Incidencia $incidencia, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        $em = $doctrine->getManager();
        $em->remove($incidencia);
        $em->flush();
        $this->addFlash("aviso", "Incidencia borrada");
        return $this->redirectToRoute("listado_clientes");
    }

    /**
     * @Route("/incidencia/editar/{id<\d+>}", name="editar_incidencia")
     */
    public function editar(Incidencia $incidenciaSelect, Request $request, ManagerRegistry $doctrine): Response {
        if($this->getUser() === null){
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidenciaSelect)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, indique el titulo de la incidencia.',
                                ]),
                    ],
                    'data' => $incidenciaSelect->getTitulo(),
                    'attr' => array(
                        'placeholder' => 'Ingrese el Titulo '
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        $incidenciaSelect->getEstado() => $incidenciaSelect->getEstado(),
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ]
                ])
                ->add('submit', SubmitType::class, array(
                    'label' => 'Modificar Incidencia'
                ))
                ->getForm();
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();

            $incidenciaSelect->setTitulo($titulo);
            $incidenciaSelect->setEstado($estado);
            $incidenciaSelect->setCliente($incidenciaSelect->getCliente());
            $incidenciaSelect->setUsuario($incidenciaSelect->getUsuario());
            $incidenciaSelect->setFechaCreacion($incidenciaSelect->getFechaCreacion());
            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash("aviso", "Incidencia Modificada");
            return $this->redirectToRoute("listado_clientes");
        }
        return $this->renderForm('incidencias/editarIncidencia.html.twig', ['form_post' => $form]);
    }

}
