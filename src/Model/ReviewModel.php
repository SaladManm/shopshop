<?php

namespace MyApp\Model;

use MyApp\Entity\Review;
use PDO;

class ReviewModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addReview(Review $review): bool
    {
        $sql = "INSERT INTO Review (product_id, user_id, rating, comment) VALUES (:product_id, :user_id, :rating, :comment)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $review->getProductId(), PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $review->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':rating', $review->getRating());
        $stmt->bindValue(':comment', $review->getComment());
        
        return $stmt->execute();
    }

    public function getReviewsByProductId(int $productId): array
    {
        $sql = "SELECT Review.*, CONCAT(User.firstName, ' ', User.lastName) AS userName FROM Review JOIN User ON Review.user_id = User.id WHERE product_id = :product_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();      
        $reviews = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $review = new Review(
                $row['id'],
                $row['product_id'],
                $row['user_id'],
                $row['rating'],
                $row['comment'],
                new \DateTime($row['created_at']),
                $row['userName'] 
            );
            $reviews[] = $review;
        }
        
        return $reviews;
    }
    
    

    public function getAverageRatingByProductId(int $productId): float
    {
        $sql = "SELECT AVG(rating) as averageRating FROM Review WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (float) $row['averageRating'] : 0;
    }
}
