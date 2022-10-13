<?php 

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService
{
  private $repo;
  private $rs;
  
   public function __construct(ProduitRepository $repo, RequestStack $rs)
   {
      $this->repo = $repo;
      $this->rs = $rs;   
   
   }

   public function add($id)
   {
     
     $session = $this->rs->getSession();

     $cart = $session->get('cart', []);
    
     if(!empty($cart[$id]))
     {
       $cart[$id]++;
     }
     else
     {
       $cart[$id] = 1;
     }


     $session->set('cart', $cart);
    
   
   }

   public function remove($id)
   {
      
   
    $session = $this->rs->getSession();
    $cart = $session->get('cart', []);

    if(!empty($cart[$id]))
    {
     unset($cart[$id]);
    }
    
    $session->set('cart', $cart);
   
    }

    public function getCartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        
        $cartWithData = [];

        foreach($cart As $id => $quantite)
        {
          $cartWithData[] = [
             'produit' => $this->repo->find($id),
             'quantite' => $quantite
          ];
        }
        return $cartWithData;
    }

    public function getTotal()
    {   
        $cartWithData = $this->getCartWithData();
        $total = 0;
        
        foreach($cartWithData as $item)
        {
            $totalUnitaire = $item['produit']->getPrix() * $item['quantite'];
            $total = $total + $totalUnitaire;
        }
        return $total;
    }
}
