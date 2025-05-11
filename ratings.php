<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Page</title> 
    <link rel="stylesheet" href="css/ratings.css">
    <link rel="shortcut icon" href="media/star.jpg" type="image/x-icon"> 
</head>
<body>
    <div class="container">
        <div class="stars">
            <span class="star" data-rating="1">&#9733;</span>
            <span class="star" data-rating="2">&#9733;</span>
            <span class="star" data-rating="3">&#9733;</span>
            <span class="star" data-rating="4">&#9733;</span>
            <span class="star" data-rating="5">&#9733;</span>
        </div>
        <input type="text" name="" id="star-numbers" disabled>
        <textarea id="reviewText" rows="10" placeholder="Write your review here..."></textarea>
        <button id="submitReview">Submit Review</button>
    </div>
    <script src="js/rating.js"></script>
</body>
</html>
