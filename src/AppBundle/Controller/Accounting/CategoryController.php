<?php
namespace AppBundle\Controller\Accounting;

use AppBundle\Form\Type\CategoryCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting/category",
     *        name="app_accounting_category",
     *        methods={"GET","POST"})
     */
    public function indexAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Categories
        $categories = $em->getRepository('AppBundle:Category')->findBy(array(), array('name' => 'ASC'));

        // Edit form
        $formEdit = $this->createForm(CategoryCollectionType::class, array('categories' => $categories));
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $data = new ArrayCollection($formEdit->getData()['categories']);
            foreach ($data as $c) {
                $em->persist($c);
            }
            foreach ($categories as $c) {
                if (!$data->contains($c)) {
                    $em->remove($c);
                }
            }
            $em->flush();

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('category.success.updated', array(), 'accounting'));

            // Redirect
            return $this->redirectToRoute('app_accounting_category');

        }

        // Render
        return $this->render(
            'accounting/category/index.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }
}
