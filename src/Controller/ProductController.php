<?php

namespace App\Controller;

use App\Exception\ProductNotFoundException;
use App\Form\ProductFormType;
use App\Model\Product\Api\ProductApiFilterFacade;
use App\Model\Product\Api\ProductApiMapper;
use App\Model\Product\ProductDataFactory;
use App\Model\Product\ProductFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private ProductFacade $productFacade;
    private ProductApiMapper $productApiMapper;
    private ProductDataFactory $productDataFactory;
    private ProductApiFilterFacade $productApiFilterFacade;

    public function __construct(ProductFacade          $productFacade,
                                ProductApiMapper       $productApiMapper,
                                ProductDataFactory     $productDataFactory,
                                ProductApiFilterFacade $productApiFilterFacade
    )
    {
        $this->productFacade = $productFacade;
        $this->productApiMapper = $productApiMapper;
        $this->productDataFactory = $productDataFactory;
        $this->productApiFilterFacade = $productApiFilterFacade;
    }

    #[Route('/product', name: 'product_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = (int)$request->get('page', 0);

        return $this->json([
            'success' => true,
            'data' => $this->productApiFilterFacade->findAll($page),
        ]);
    }

    #[Route('/product/search', name: 'product_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $text = $request->get('text');

        if (empty($text) || strlen($text) < 3) {
            return $this->json([
                'success' => false,
                'message' => 'error',
            ]);
        }

        return $this->json([
            'success' => true,
            'data' => $this->productApiFilterFacade->findAllByText($text),
        ]);
    }

    #[Route('/product', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->productDataFactory->create();

        $form = $this->createForm(ProductFormType::class, $data, []);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->json([
                'success' => false,
                'message' => 'error',
//                'messages' => $form->getErrors(),
            ]);
        }

        // add images
        // add category
        $product = $this->productFacade->createFromData($data);

        return $this->json([
            'success' => true,
            'id' => $product->getId(),
            'message' => 'success',
        ]);
    }

    #[Route('/product/{id}', name: 'product_read', methods: ['GET'])]
    public function read(Request $request, int $id): JsonResponse
    {
        try {
            $productArray = $this->productApiFilterFacade->getById($id);
        } catch (ProductNotFoundException) {
            return $this->json([
                'success' => false,
                'message' => 'error',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $productArray,
        ]);
    }

    #[Route('/product/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productFacade->getById($id);
        } catch (ProductNotFoundException) {
            return $this->json([
                'success' => false,
                'message' => 'error',
            ], 404);
        }

        $data = $this->productDataFactory->createFromProduct($product);

        $form = $this->createForm(ProductFormType::class, $data, []);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->json([
                'success' => false,
                'message' => 'error',
//                'messages' => $form->getErrors(),
            ]);
        }

        // add images
        // add category
        $this->productFacade->updateFromData($product, $data);

        return $this->json([
            'success' => true,
            'data' => $this->productApiMapper->createArrayFromProduct($product),
        ]);
    }

    #[Route('/product/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            $this->productFacade->deleteById($id);
        } catch (ProductNotFoundException) {
            return $this->json([
                'success' => false,
                'message' => 'error',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'message' => 'success',
        ]);
    }
}
