<?php

declare(strict_types=1);

namespace MyApp\Controller;

use MyApp\Service\DependencyContainer;
use Twig\Environment;
use MyApp\Entity\Type;
use MyApp\Entity\Product;
use MyApp\Entity\User;
use MyApp\Entity\Review; 
use MyApp\Model\TypeModel;
use MyApp\Model\ProductModel;
use MyApp\Model\UserModel;
use MyApp\Model\CartModel;
use MyApp\Model\ReviewModel;

class DefaultController
{
    private $twig;
    private $typeModel;
    private $productModel;
    private $userModel;
    private $cartModel;
    private $reviewModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->typeModel = $dependencyContainer->get('TypeModel');
        $this->productModel = $dependencyContainer->get('ProductModel');
        $this->userModel = $dependencyContainer->get('UserModel');
        $this->cartModel = $dependencyContainer->get('CartModel');
        $this->reviewModel = $dependencyContainer->get('ReviewModel');
    }

    public function home()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/home.html.twig', ['types' => $types]);
    }
    

    public function contact()
    {
        echo $this->twig->render('defaultController/contact.html.twig', []);
    }


    public function types()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/types.html.twig', ['types' => $types]);
    }

    public function produit()
    {
        $produits = $this->productModel->getAllProduct();
        echo $this->twig->render('defaultController/produit.html.twig', ['produits' => $produits]);
    }

    public function user()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/user.html.twig', ['users' => $users]);
    }

    public function cart() {
        $carts = $this->cartModel->getAllCarts();
        echo $this->twig->render('defaultController/cart.html.twig', ['carts' => $carts]);
    }    

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error403()
    {
        echo $this->twig->render('defaultController/error403.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($label)) {
                $type = new Type(intVal($id), $label);
                $success = $this->typeModel->updateType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $type = $this->typeModel->getOneType(intVal($id));
        echo $this->twig->render('defaultController/updateType.html.twig', ['type' => $type]);
    }

    public function updateUser()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        // Vérifie si les données nécessaires sont présentes
        if (!empty($email)) {
            // Récupère les données de l'utilisateur par son ID
            $user = $this->userModel->getOneUser(intVal($id));

            // Si l'utilisateur n'est pas trouvé, redirige avec un message d'erreur
            if (!$user) {
                $_SESSION['message'] = 'Utilisateur introuvable.';
                header('Location: index.php?page=user');
                exit;
            }

            // Met à jour les données de l'utilisateur avec les nouvelles valeurs
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);

            // Si un nouveau mot de passe est fourni, le met à jour
            if (!empty($password)) {
                // Hache le nouveau mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);
            }

            // Effectue la mise à jour de l'utilisateur dans la base de données
            $success = $this->userModel->updateUser($user);

            if ($success) {
                $_SESSION['message'] = 'Utilisateur mis à jour avec succès.';
                header('Location: index.php?page=user');
                exit;
            } else {
                $_SESSION['message'] = 'Erreur lors de la mise à jour de l\'utilisateur.';
                header('Location: index.php?page=user');
                exit;
            }
        } else {
            $_SESSION['message'] = 'Erreur : données invalides.';
            header('Location: index.php?page=user');
            exit;
        }
    } else {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Récupère les données de l'utilisateur par son ID
        $user = $this->userModel->getOneUser(intVal($id));

        // Si l'utilisateur n'est pas trouvé, redirige avec un message d'erreur
        if (!$user) {
            $_SESSION['message'] = 'Utilisateur introuvable.';
            header('Location: index.php?page=user');
            exit;
        }

        echo $this->twig->render('defaultController/updateUser.html.twig', ['user' => $user]);
    }
}

public function addProductToCart($request)
{
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $cartId = $_POST['cartId'];
        $productId = $_POST['productId'];
        $quantity = $_POST['quantity'];

        // Créer une instance du modèle de panier
        $cartModel = new CartModel();

        // Si un nouveau panier doit être créé
        if (empty($cartId)) {
            // Récupérer le nom du nouveau panier
            $newCartName = $_POST['newCartName'];

            // Créer un nouveau panier et récupérer son ID
            $cartId = $cartModel->createCart($newCartName);
        }

        // Ajouter le produit au panier
        $cartModel->addProductToCart($cartId, $productId, $quantity);

        // Rediriger vers une autre page (par exemple, la page du panier)
        header('Location: index.php?page=myCart');
        exit;
    }

    // Si le formulaire n'a pas été soumis ou s'il y a eu une erreur, rediriger vers une autre page
    header('Location: index.php?page=home');
    exit;
}
public function myCart()
{
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = 'Vous devez être connecté pour accéder à votre panier.';
        header('Location: index.php?page=login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $carts = $this->cartModel->getCartsByUserId($userId);

    echo $this->twig->render('defaultController/myCart.html.twig', ['carts' => $carts]);
}

public function cartProducts()
{
    $cartId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!$cartId || !is_numeric($cartId)) {
        $_SESSION['message'] = 'ID de panier non spécifié ou invalide.';
        header('Location: index.php?page=myCart');
        exit;
    }

    $cart = $this->cartModel->getCartById((int) $cartId);
    if (!$cart) {
        $_SESSION['message'] = 'Panier non trouvé.';
        header('Location: index.php?page=myCart');
        exit;
    }

    $products = $this->cartModel->getProductsInCart((int) $cartId);

    echo $this->twig->render('defaultController/cartProducts.html.twig', ['cart' => $cart, 'products' => $products]);
}

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING) ?? '';
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING) ?? '';
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);
    
            // Vérifiez si un utilisateur avec le même email existe déjà
            $existingUser = $this->userModel->getUserByEmail($email);
    
            if ($existingUser) {
                $_SESSION['message'] = 'Un utilisateur avec cet email existe déjà.';
                header('Location: index.php?page=addUser');
                exit;
            }
    
            if (!empty($email) && !empty($password) && ($password === $confirmPassword)) {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = new User(
                    null, // $id (nullable)
                    $email, // $email
                    $lastName, // $lastName
                    $firstName, // $firstName
                    $hashedPassword, // $password
                    '', // $address (valeur par défaut, vous pouvez ajuster en fonction de votre logique)
                    '', // $postalCode (valeur par défaut)
                    '', // $city (valeur par défaut)
                    '', // $phone (valeur par défaut)
                    ['user']// $roles
                );
    
                // Enregistrez les données de l'utilisateur dans la base de données
                $result = $this->userModel->createUser($user);
                if ($result) {
                    header('Location: index.php?page=login');
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur lors de l\'inscription';
                }
            } else {
                $_SESSION['message'] = 'Erreur : données invalides ou les mots de passe ne correspondent pas';
            }
    
            header('Location: index.php?page=addUser');
            exit;
        }
    
        echo $this->twig->render('defaultController/addUser.html.twig', []);
    }
    
    
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
    
            $user = $this->userModel->getUserByEmail($email);
    
            if (!$user) {
                $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                header('Location: index.php?page=login');
                exit;
            } else {
                if ($user->verifyPassword($password)) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['login'] = $user->getEmail();
                    $_SESSION['roles'] = $user->getRoles();
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                    header('Location: index.php?page=login');
                    exit;
                }
            }
        }
    
        echo $this->twig->render('defaultController/login.html.twig', []);
    }
    
    
    public function logout() {
        $_SESSION = array(); session_destroy(); header('Location: index.php'); exit;
        }

        public function deleteUser()
        {
            // Récupérez l'ID de l'utilisateur à supprimer depuis les paramètres GET
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
            // Ajoutez un var_dump pour afficher l'ID
            var_dump('User ID to delete: ' . $id);
        
            // Assurez-vous que l'ID est valide
            if (!is_numeric($id)) {
                // Ajoutez un var_dump pour afficher un message d'erreur si l'ID n'est pas valide
                var_dump('Invalid User ID');
                // Redirigez ou affichez un message d'erreur ici, si nécessaire
            }
        
            // Supprimez l'utilisateur en utilisant votre modèle UserModel
            $success = $this->userModel->deleteUser(intVal($id));
        
            // Ajoutez un var_dump pour afficher le résultat de la suppression
            var_dump('User deletion result: ' . $success);
        
            // Redirigez en fonction du résultat de la suppression
            if ($success) {
                // Ajoutez un var_dump pour afficher un message de succès si la suppression réussit
                var_dump('User deleted successfully');
                header('Location: index.php?page=user');
                exit;
            } else {
                // Ajoutez un var_dump pour afficher un message d'erreur si la suppression échoue
                var_dump('Error deleting user');
                // Redirigez ou affichez un message d'erreur ici, si nécessaire
            }
        }

        public function addType()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
                if (!empty($label)) {
                    $type = new Type(null, $label);
                    $success = $this->typeModel->createType($type);
                    if ($success) {
                        header('Location: index.php?page=types');
                    }
                }
            }
            
            echo $this->twig->render('defaultController/addType.html.twig', []);
        }
        
        
        public function deleteType()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                
                if (is_numeric($id)) {
                    // Supprimez d'abord les produits liés à ce type
                    $this->productModel->deleteProductsByTypeId(intVal($id));
                    
                    // Ensuite, supprimez le type
                    $success = $this->typeModel->deleteType(intVal($id));
                    
                    if ($success) {
                        header('Location: index.php?page=types');
                        exit;
                    } else {
                        $_SESSION['message'] = 'Erreur lors de la suppression du type';
                    }
                } else {
                    $_SESSION['message'] = 'ID de type invalide';
                }
            }
            
            echo $this->twig->render('defaultController/types.html.twig', []);
        }
        
        public function deleteProductsByTypeId(int $typeId): bool
        {
            $sql = "DELETE FROM Product WHERE id_Type = :typeId";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':typeId', $typeId, PDO::PARAM_INT);
            return $stmt->execute();
        }
        
    
        public function profile()
        {
            // Vérifier si l'utilisateur est connecté et si l'ID utilisateur est défini
            if (!isset($_SESSION['user_id'])) {
                // Rediriger vers la page de connexion ou afficher un message d'erreur
                $_SESSION['message'] = 'Vous devez être connecté pour accéder à cette page.';
                header('Location: index.php?page=login');
                exit;
            }
    
            // Récupérer l'ID utilisateur et s'assurer qu'il est de type entier
            $userId = (int) $_SESSION['user_id'];
    
            // Récupérer les informations de l'utilisateur
            $user = $this->userModel->getUserById($userId);
            if ($user === null) {
                // Gérer le cas où l'utilisateur n'est pas trouvé
                $_SESSION['message'] = 'Utilisateur introuvable.';
                header('Location: index.php');
                exit;
            }
    
            // Récupérer l'historique des commandes de l'utilisateur
            $orders = $this->cartModel->getOrdersByUserId($userId);
    
            // Afficher le profil de l'utilisateur avec son historique de commandes
            echo $this->twig->render('defaultController/profile.html.twig', ['user' => $user, 'orders' => $orders]);
        }
    
        public function updatePassword()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $oldPassword = $_POST['oldPassword'];
                $newPassword = $_POST['newPassword'];
                $confirmPassword = $_POST['confirmPassword'];
                $userId = $_SESSION['user_id'];
    
                $user = $this->userModel->getUserById($userId);
    
                // Vérifier l'ancien mot de passe
                if (!$user->verifyPassword($oldPassword)) {
                    $_SESSION['message'] = 'L\'ancien mot de passe est incorrect.';
                    header('Location: index.php?page=profile');
                    exit;
                }
    
                // Vérifier la correspondance des nouveaux mots de passe
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['message'] = 'Les nouveaux mots de passe ne correspondent pas.';
                    header('Location: index.php?page=profile');
                    exit;
                }
    
                // Mettre à jour le mot de passe
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);
                $this->userModel->updateUser($user);
    
                $_SESSION['message'] = 'Mot de passe mis à jour avec succès.';
                header('Location: index.php?page=profile');
                exit;
            }
        }
    

public function updateProduit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $typeLabel = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

            // Vérifiez que toutes les données nécessaires sont présentes
            if (!$id || !$name || !$description || !$price || !$typeLabel) {
                $_SESSION['message'] = 'Erreur : données invalides';
                header('Location: index.php?page=updateProduit&id=' . $id);
                exit;
            }

            // Créez un objet Product avec les données mises à jour
            $type = new Type(null, $typeLabel); // Vous devrez peut-être ajuster cela en fonction de votre modèle de données
            $updatedProduct = new Product(intVal($id), $name, $description, floatVal($price), $type);

            // Mettez à jour le produit dans la base de données
            $success = $this->productModel->updateProduct($updatedProduct);

            if ($success) {
                $_SESSION['message'] = 'Produit mis à jour avec succès';
                header('Location: index.php?page=produit');
                exit;
            } else {
                $_SESSION['message'] = 'Erreur lors de la mise à jour du produit';
                header('Location: index.php?page=updateProduit&id=' . $id);
                exit;
            }
        } else {
            // Si la requête n'est pas de type POST, affichez la page avec le formulaire
            $productId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $product = $this->productModel->getOneProduct((int) $productId);

            if ($product) {
                echo $this->twig->render('defaultController/updateProduit.html.twig', ['produit' => $product]);
                exit;
            } else {
                $_SESSION['message'] = 'Produit non trouvé';
                header('Location: index.php?page=products');
                exit;
            }
        }
    }
    public function deleteProduit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            // Assurez-vous que la suppression a réussi avant de rediriger
            $success = $this->productModel->deleteProduct(intVal($id));

            if ($success) {
                $_SESSION['message'] = 'Produit supprimé avec succès';
            } else {
                $_SESSION['message'] = 'Erreur lors de la suppression du produit';
            }

            header('Location: index.php?page=produit');
            exit;
        } else {
            // Gérer d'autres méthodes HTTP si nécessaire
            $_SESSION['message'] = 'Méthode HTTP non autorisée';
            header('Location: index.php?page=produit');
            exit;
        }
    }

    public function addCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'] ?? null;
            $status = $_POST['status'] ?? 'active';
    
            if ($userId) {
                try {
                    $cartId = $this->cartModel->createCart((int) $userId, $status);
                    $_SESSION['message'] = "Le panier a été créé avec succès. ID du panier: $cartId";
                    header('Location: index.php?page=cart');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['message'] = "Erreur lors de la création du panier: " . $e->getMessage();
                }
            }
        }
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/addcart.html.twig', ['users' => $users]);
    }
    

    
    public function additem() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = (int) $_POST['cartId'];
            $productId = $_POST['productId']; 
            $quantity = $_POST['quantity'];
            try {
                $cart = $this->cartModel->getCartById($cartId);
                $product = $this->productModel->getProductById($productId);
                $this->cartModel->addItemToCart($cart, $product, $quantity); 
                $_SESSION['message'] = "Produit ajouté avec succès au panier.";
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de l'ajout du produit au panier: " . $e->getMessage();
            }
        }
        $carts = $this->cartModel->getAllCarts();
        $products = $this->productModel->getAllProduct();
    
        echo $this->twig->render('defaultController/additem.html.twig', ['carts' => $carts, 'products' => $products]);
    }
    

    
    
    public function deleteCartConfirm()
    {
        $cartId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($cartId !== null && $cartId !== false) {
            $cartId = (int) $cartId;
            if ($this->cartModel->deleteCartConfirm($cartId)) {
                $_SESSION['message'] = 'Panier supprimé avec succès';
            } else {
                $_SESSION['message'] = 'Erreur lors de la suppression du panier';
            }
        } else {
            $_SESSION['message'] = 'ID du panier non spécifié ou invalide';
        }
        header('Location: index.php?page=cart');
        exit;
    }
    
    public function updateCart()
    {
        $cartId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
        if ($cartId) {
            $cartId = (int)$cartId;
            $cart = $this->cartModel->getCartById($cartId);
    
            if ($cart) {
                echo $this->twig->render('defaultController/updateCart.html.twig', ['cart' => $cart]);
            } else {
                $_SESSION['message'] = 'Panier non trouvé.';
                header('Location: index.php?page=cart');
                exit;
            }
        } else {
            $_SESSION['message'] = 'ID du panier non spécifié.';
            header('Location: index.php?page=cart');
            exit;
        }
    }
    public function addReviewForm()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour ajouter un avis.';
            header('Location: index.php?page=login');
            exit;
        }
    
        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$productId) {
            $_SESSION['message'] = 'ID de produit non spécifié ou invalide.';
            header('Location: index.php?page=produit');
            exit;
        }
    
        $product = $this->productModel->getOneProduct($productId);
        if (!$product) {
            $_SESSION['message'] = 'Produit non trouvé.';
            header('Location: index.php?page=produit');
            exit;
        }

        echo $this->twig->render('defaultController/addReviewForm.html.twig', ['product' => $product]);
    }
    
    
    public function submitReview()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour soumettre un avis.';
            header('Location: index.php?page=login');
            exit;
        }
    
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_FLOAT);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
        $userId = $_SESSION['user_id'];
    
        if ($productId && $rating !== false && $comment) {
            $review = new Review(null, $productId, $userId, $rating, $comment, new \DateTime());
            $success = $this->reviewModel->addReview($review);
    
            if ($success) {
                $_SESSION['message'] = 'Avis ajouté avec succès.';
                header("Location: index.php?page=produit");
                exit;
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'ajout de l\'avis.';
            }
        } else {
            $_SESSION['message'] = 'Données invalides.';
        }
    
        header("Location: index.php?page=addReviewForm&id=$productId");
        exit;
    }
    
    public function viewReviews()
    {
        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$productId) {
            $_SESSION['message'] = 'Produit non spécifié.';
            header('Location: index.php');
            exit;
        }
    
        $reviews = $this->reviewModel->getReviewsByProductId($productId);
        $product = $this->productModel->getOneProduct($productId);
    
        if (!$product) {
            $_SESSION['message'] = 'Produit non trouvé.';
            header('Location: index.php');
            exit;
        }
    
        echo $this->twig->render('defaultController/viewReviews.html.twig', ['reviews' => $reviews, 'product' => $product]);
    }
    
    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);        
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $typeId = filter_input(INPUT_POST, 'type_id', FILTER_SANITIZE_NUMBER_INT);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT); // Ajout du stock
        
            // Vérifiez que toutes les données nécessaires sont présentes
            if (!$name || !$description || !$price || !$typeId || !$stock) {
                $_SESSION['message'] = 'Erreur : données invalides';
                header('Location: index.php?page=addProduct');
                exit;
            }
        
            // Créez un objet Product avec les données fournies
            $type = $this->typeModel->getOneType(intVal($typeId));
            if (!$type) {
                $_SESSION['message'] = 'Type de produit invalide';
                header('Location: index.php?page=addProduct');
                exit;
            }
        
            $price = (float) $price;
            $stock = (int) $stock;
        
            // Créez un nouvel identifiant entre 1 et 100000000000000
            $id = mt_rand(1, 100000000000000);
        
            // Créez un nouvel objet Product avec un ID généré et le stock
            $product = new Product($id, $name, $description, $price, $type, $stock);
        
            // Ajoutez le produit à la base de données
            $success = $this->productModel->addProduct($product);
        
            if ($success) {
                $_SESSION['message'] = 'Produit ajouté avec succès';
                header('Location: index.php?page=produit');
                exit;
            } else {
                $_SESSION['message'] = 'Erreur lors de l\'ajout du produit';
                header('Location: index.php?page=addProduct');
                exit;
            }
        }
        
        // Récupérez tous les types pour afficher dans le formulaire
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/addProduct.html.twig', ['types' => $types]);
    }

public function productType($typeId)
{

    $products = $this->productModel->getProductsByType($typeId);
    echo $this->twig->render('productType.html.twig', ['products' => $products]);
}


}

