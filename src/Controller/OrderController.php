<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        SessionInterface $session
    ): Response {

        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        if ($request->isMethod('POST')) {

            $name = trim($request->request->get('customerName', ''));
            $email = trim($request->request->get('customerEmail', ''));
            $phone = trim($request->request->get('customerPhone', ''));
            $address = trim($request->request->get('customerAddress', ''));

            if (
                $name === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                $phone === '' ||
                $address === ''
            ) {
                $this->addFlash('danger', 'Veuillez remplir correctement tous les champs.');
                return $this->redirectToRoute('app_order');
            }

            $order = new Order();

            $order->setCustomerName($name);
            $order->setCustomerEmail($email);
            $order->setCustomerPhone($phone);
            $order->setCustomerAddress($address);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setStatus('En attente');

            $totalCents = 0;

            foreach ($cart as $id => $quantity) {

                $product = $productRepository->find($id);

                if (!$product || $quantity < 1) {
                    continue;
                }

                $item = new OrderItem();

                $item->setProduct($product);
                $item->setQuantity((int) $quantity);
                $item->setPrice($product->getPrice());
                $item->setCustomerOrder($order);

                $entityManager->persist($item);

                $totalCents += (int) round((float) $product->getPrice() * 100) * (int) $quantity;
            }

            if ($totalCents === 0) {
                $this->addFlash('danger', 'Aucun produit valide dans votre panier.');
                return $this->redirectToRoute('app_cart_index');
            }

            $order->setTotal(number_format($totalCents / 100, 2, '.', ''));

            $entityManager->persist($order);
            $entityManager->flush();

            $session->remove('cart');
            $session->set('cart_count', 0);

            return $this->render('order/index.html.twig', [
                'order' => $order,
            ]);
        }

        return $this->render('order/index.html.twig', [
            'order' => null,
        ]);
    }
}

