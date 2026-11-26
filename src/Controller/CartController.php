<?php

namespace App\Controller;


use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    
#[Route('/cart', name: 'app_cart_index')]
public function index(
    SessionInterface $session,
    ProductRepository $productRepository
): Response {
    // Récupérer le panier
    $cart = $session->get('cart', []);

    // Préparer les produits du panier
    $cartWithData = [];
    $total = 0;

    foreach ($cart as $id => $quantity) {
        $product = $productRepository->find($id);

        if ($product) {
            $price = (float) $product->getPrice();

            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
            ];

            $total += $price * $quantity;
        }
    }

    return $this->render('cart/index.html.twig', [
        'items' => $cartWithData,
        'total' => $total,
    ]);
}


    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function add(
        int $id,
        ProductRepository $productRepository,
        SessionInterface $session
    ): Response {
        // Récupérer le produit dans la base de données
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // Récupérer le panier dans la session
        $cart = $session->get('cart', []);

        // Ajouter le produit ou augmenter sa quantité
        $cart[$id] = ($cart[$id] ?? 0) + 1;

        // Enregistrer le panier dans la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('app_cart_index');
    }


    #[Route('/cart/increase/{id}', name: 'app_cart_increase', methods: ['POST'])]
    public function increase(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]++;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods: ['POST'])]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]--;

            if ($cart[$id] <= 0) {
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }


    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        // Supprimer le produit du panier
        unset($cart[$id]);

        // Enregistrer les modifications
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit supprimé du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    // unset() supprime le produit.
    // set() enregistre le nouveau panier.
    // addFlash() affiche un message de confirmation.


    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(SessionInterface $session): Response
    {
        // Vider complètement le panier
        $session->remove('cart');

        $this->addFlash('success', 'Votre panier a été vidé.');

        return $this->redirectToRoute('app_cart_index');
    }
    //remove('cart') supprime les données du panier dans la session.
    //Le message confirme la suppression.
    //Le visiteur revient sur la page du panier

}
