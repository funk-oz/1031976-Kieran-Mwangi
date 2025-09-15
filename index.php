<body>
<?php include "includes/head.php" ?>

    <style>
          body {
            overflow-x: hidden;
            margin: 0;
            padding: 0; /* Prevent horizontal scroll */
        }
      
      .product-container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }
    .product-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start; /* Ensures minimum width for 4 columns */
        max-height: 600px; /* Limit height to show 2 rows */
        overflow-y: auto; /* Make it scrollable vertically */
        padding: 20px;
        scrollbar-width: thin;
        scrollbar-color: #4CAF50 #f1f1f1;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Custom scrollbar for products grid */
    .product-grid::-webkit-scrollbar {
        width: 8px;
    }
    
    .product-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .product-grid::-webkit-scrollbar-thumb {
        background: #4CAF50;
        border-radius: 10px;
    }
    
    .product-grid::-webkit-scrollbar-thumb:hover {
        background: #45a049;
    }
    .product-card {
        width: calc(25% - 20px);
        min-width: 200px;
        background-color: transparent;
        border: 1px solid #ffc107;
        border-radius: 5px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .product-image {
        width: 125px;
        height: 125px;
        object-fit: cover;
        margin-bottom: 10px;
    }
    .product-title {
        font-size: 1em;
        margin-bottom: 10px;
        text-align: center;
    }
    .product-price {
        color: #82E0AA;
        font-size: 1.2em;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .product-brand {
        font-size: 0.9em;
        color: #6c757d;
        margin-bottom: 10px;
    }
    .btn-details {
        display: inline-block;
        padding: 8px 15px;
        background-color: transparent;
        color: #28a745;
        text-decoration: none;
        border: 1px solid #28a745;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
        .carousel {
            width: 100%;
            height: 350px;
            position: relative;
            overflow: hidden;
        }
        .carousel-item {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .carousel-item.active {
            opacity: 1;
        }
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.3);
            color: white;
            padding: 10px;
            width: 36px;
            height: 36px;
            text-decoration: none;
            font-size: 18px;
            line-height: 1;
            border-radius: 50%;
            transition: background 0.3s ease;
        }
        .carousel-control:hover {
            background: rgba(0, 0, 0, 0.6);
        }
        .carousel-control-prev { left: 10px; }
        .carousel-control-next { right: 10px; }
    </style>

    <div class="carousel">
        <div class="carousel-item active">
            <img src="images/trans1.png" alt="Image 1">
        </div>
        <div class="carousel-item">
            <img src="images/trans2.png" alt="Image 2">
        </div>
        <div class="carousel-item">
            <img src="images/trans3.jpg" alt="Image 3">
        </div>
        <div class="carousel-item">
            <img src="images/trans4.gif" alt="Image 4">
        </div>
        <div class="carousel-item">
            <img src="images/trans5.png" alt="Image 5">
        </div>
        <a href="#" class="carousel-control carousel-control-prev" id="prevBtn">&lt;</a>
        <a href="#" class="carousel-control carousel-control-next" id="nextBtn">&gt;</a>
    </div>

    <script>
        const items = document.querySelectorAll('.carousel-item');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        let currentIndex = 0;

        function showSlide(index) {
            items[currentIndex].classList.remove('active');
            currentIndex = (index + items.length) % items.length;
            items[currentIndex].classList.add('active');
        }

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showSlide(currentIndex - 1);
        });

        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showSlide(currentIndex + 1);
        });

        // Auto-play
        setInterval(() => showSlide(currentIndex + 1), 5000);
    </script>

    <br>
    <br>

   
        <style>
           .container {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 20px;
  padding: 80px;
  font-family: Noto Sans, sans-serif;
  font-weight: lighter;
}

.item {
  display: flex;
  align-items: flex-start;
}

.item svg {
  margin-right: 20px;
  height: 44px;
  width: 44px;
  flex-shrink: 0;
}

.title {
  font-weight: bold;
  color: #198754;
}

.text {
  color: #000000;
}

/* Media queries */


@media (max-width: 768px) {
 .container {
    grid-template-columns: repeat(3, 1fr);
  }
}


@media (max-width: 480px) {
 .container {
    grid-template-columns: repeat(2, 1fr);
  }
 .item svg {
    height: 30px;
    width: 30px;
  }
 .title {
    font-size: 18px;
  }
 .text {
    font-size: 14px;
  }
}
            
        </style>

        <div class="container">
            <div class="item">
                <img src="images/genuine pro.svg" alt="Genuine Products Icon">
                <div>
                    <div class="title">Genuine Products</div>
                    <div class="text">All our products are 100% genuine.</div>
                </div>
            </div>
            <div class="item">
                <img src="images/easy pay.svg" alt="Easy Payments Icon">
                <div>
                    <div class="title">Easy Payments</div>
                    <div class="text">Pay by Mpesa, Visa or MasterCard.</div>
                </div>
            </div>
            <div class="item">
                <img src="images/customer su.svg" alt="Customer Support Icon">
                <div>
                    <div class="title">Customer Support</div>
                    <div class="text">Available 7 days a week.</div>
                </div>
            </div>
            <div class="item">
                <img src="images/country.svg" alt="Nationwide Delivery Icon">
                <div>
                    <div class="title">Countrywide Delivery</div>
                    <div class="text">From a network of over 100+ stores.</div>
                </div>
            </div>
            <div class="item">
                <img src="images/express.svg" alt="Express Delivery Icon">
                <div>
                    <div class="title">Express Delivery</div>
                    <div class="text">2-hour delivery if ordered by 4pm</div>
                </div>
            </div>
        </div>

        <br>
       
        <hr>
        <br>
       

        <style>
    /* Custom CSS - Categories now use external CSS from index.css for horizontal layout */
        
        /* Force horizontal layout for categories */
        #cards-container {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            overflow-x: auto;
            gap: 20px;
            padding: 20px 0;
            margin: 0 15px;
            white-space: nowrap;
            align-items: stretch;
            justify-content: flex-start;
            scroll-behavior: smooth;
            width: calc(100% - 30px);
            box-sizing: border-box;
            height: auto !important;
            min-height: 300px;
        }
        
        #cards-container .card {
            flex: 0 0 calc(25% - 15px) !important; /* Exactly 4 cards visible */
            width: calc(25% - 15px) !important;
            min-width: calc(25% - 15px) !important;
            max-width: calc(25% - 15px) !important;
            height: auto;
            border: 1px solid #e0e0e0; /* Clean border */
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #f8f9fa;
            margin: 0 !important;
            float: none !important;
            display: block !important;
        }
        
        #cards-container .card-body {
            padding: 15px;
            text-align: center;
            overflow: hidden;
            word-wrap: break-word;
        }
        
        #cards-container .card h5 {
            margin: 10px 0;
            color: #333;
            font-size: 1.1em;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        #cards-container .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin: 10px 0;
            display: block;
        }
        
        #cards-container .card button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        #cards-container .card button:hover {
            background-color: #45a049;
        }
        
        #cards-container .card .discount-badge {
            display: inline-block;
            background-color: #ffeb3b;
            color: #333;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        /* Scroll buttons */
        .scroll-btn {
            z-index: 1000;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 8px; /* Reduced from 50% to 8px for subtle rounded corners */
            background: white;
            border: 2px solid #4CAF50;
            color: #4CAF50;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .scroll-btn:hover {
            background: #4CAF50;
            color: white;
        }
        
        .left-scroll-btn {
            left: 0;
        }
        
        .right-scroll-btn {
            right: 0;
        }
        
        /* Responsive breakpoints */
        @media (max-width: 1200px) {
            #cards-container .card {
                flex: 0 0 calc(33.333% - 15px) !important; /* 3 cards visible on medium screens */
                width: calc(33.333% - 15px) !important;
                min-width: calc(33.333% - 15px) !important;
                max-width: calc(33.333% - 15px) !important;
            }
        }
        
        @media (max-width: 768px) {
            #cards-container .card {
                flex: 0 0 calc(50% - 15px) !important; /* 2 cards visible on small screens */
                width: calc(50% - 15px) !important;
                min-width: calc(50% - 15px) !important;
                max-width: calc(50% - 15px) !important;
            }
            
            #cards-container {
                gap: 15px;
                margin: 0 10px;
            }
        }
        
        @media (max-width: 480px) {
            #cards-container .card {
                flex: 0 0 calc(100% - 15px) !important; /* 1 card visible on mobile */
                width: calc(100% - 15px) !important;
                min-width: calc(100% - 15px) !important;
                max-width: calc(100% - 15px) !important;
            }
            
            #cards-container {
                gap: 10px;
                margin: 0 5px;
            }
        }
    </style>

<h2 style="margin: 20px 0; text-align: center; color: #2c3e50;">Shop By Category</h2>

<div class="categories-container">
    <button class="scroll-btn left-scroll-btn" onclick="scrollCategories('left')">
        <i class="fas fa-chevron-left"></i>
    </button>
    
    <div id="cards-container">
        <?php
        $categories = get_active_categories();
        $discounts = ['UPTO 30% OFF', 'UPTO 25% OFF', 'UPTO 40% OFF', 'UPTO 20% OFF', 'UPTO 35% OFF'];
        $discount_index = 0;
        
        foreach ($categories as $category) {
            $discount = $discounts[$discount_index % count($discounts)];
            $discount_index++;
        ?>
        <div class="card">
            <div class="card-body">
                <span class="discount-badge"><?php echo $discount; ?></span>
                <h5><?php echo htmlspecialchars($category['category_name']); ?></h5>
                <a href="search.php?cat=<?php echo urlencode($category['category_name']); ?>">
                    <img src="images/<?php echo $category['category_image'] ? htmlspecialchars($category['category_image']) : 'default-category.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($category['category_name']); ?>">
                </a>
                <a href="search.php?cat=<?php echo urlencode($category['category_name']); ?>">
                    <button class="view-category-btn">
                        View Products
                    </button>
                </a>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <button class="scroll-btn right-scroll-btn" onclick="scrollCategories('right')">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<style>
/* Categories Container */
.categories-container {
    position: relative;
    margin: 20px 0;
    padding: 0 40px;
}

/* Scroll Buttons */
.scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid #4CAF50;
    color: #4CAF50;
    font-size: 16px;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.scroll-btn:hover {
    background: #4CAF50;
    color: white;
}

.left-scroll-btn {
    left: 0;
}

.right-scroll-btn {
    right: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .categories-container {
        padding: 0 30px;
    }
    
    .scroll-btn {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
}
</style>

<script>
function scrollCategories(direction) {
    const container = document.getElementById('cards-container');
    const scrollAmount = 300; // Adjust this value to control scroll distance
    
    if (direction === 'left') {
        container.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    } else {
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}

// Hide scroll buttons when at the edges
const container = document.getElementById('cards-container');
const leftBtn = document.querySelector('.left-scroll-btn');
const rightBtn = document.querySelector('.right-scroll-btn');

function updateButtonVisibility() {
    leftBtn.style.display = container.scrollLeft <= 0 ? 'none' : 'flex';
    rightBtn.style.display = container.scrollLeft >= (container.scrollWidth - container.clientWidth - 5) ? 'none' : 'flex';
}

container.addEventListener('scroll', updateButtonVisibility);
window.addEventListener('resize', updateButtonVisibility);

// Initial check
updateButtonVisibility();
</script>

        <br>
        <br>
        <hr>
        <br>
        <br>
      

        <h2 style="margin-top: 10px; text-align: center; height: 10%">Products</h2>
        <div class="product-grid">
    <?php
    $data = all_products();
    $num = sizeof($data);
    for ($i = 0; $i < $num; $i++) { // Show ALL products, not just 8
    ?>
        <div class="product-card">
            <img src="images/<?php echo $data[$i]['item_image'] ?>" alt="<?php echo $data[$i]['item_title'] ?>" class="product-image">
            <h3 class="product-title"><?php echo strlen($data[$i]['item_title']) > 20 ? substr($data[$i]['item_title'], 0, 20) . "..." : $data[$i]['item_title'] ?></h3>
            <p class="product-price">Ksh<?php echo $data[$i]['item_price'] ?></p>
            <p class="product-brand">Brand: <?php echo $data[$i]['item_brand'] ?></p>
            <a href="product.php?product_id=<?php echo $data[$i]['item_id'] ?>" class="btn-details">More details</a>
        </div>
    <?php
    }
    ?>
</div>
        <br>
        <br>
        <div class="container-fluid">
            <style>
                .image-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    overflow: hidden;
                    margin-top: 20px;
                }
                .image-container img {
                    width: 100%;
                    height: auto;
                    max-width: 100%;
                    object-fit: contain;
                    overflow: hidden;
                    margin-top: 20px;
                }
                .image-container::-webkit-scrollbar {
                    display: none;
                }
                .image-container {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>
        </div>
    </div>
    
    <br>

    <?php include "includes/footer.php"; ?>
</body>
