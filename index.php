<?php
// Use the enhanced session management from core.php
require_once 'src/settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Taste of Africa - Authentic Flavors</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
	<link href="public/css/index.css" rel="stylesheet">
</head>
<body>
	<!-- Navigation Menu -->
	<div class="menu-tray">
		<div class="container">
			<i class="fas fa-utensils me-2" style="color: var(--primary-orange);"></i>
			<?php if (is_user_logged_in()): ?>
				<span class="welcome-message me-2">
					<i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars(get_user_name()); ?>!
					<?php if (is_user_admin()): ?>
						<span style="color: var(--accent-yellow); font-weight: bold;">
							<i class="fas fa-crown"></i> Admin
						</span>
					<?php endif; ?>
				</span>
				<?php if (is_user_admin()): ?>
					<a href="admin/dashboard.php" class="btn btn-sm btn-warning" style="margin-right: 8px;">
						<i class="fas fa-tachometer-alt"></i> Admin Panel
					</a>
				<?php endif; ?>
				<a href="login/logout.php" class="btn btn-sm btn-danger-custom">
					<i class="fas fa-sign-out-alt"></i> Logout
				</a>
			<?php else: ?>
				<a href="login/register.php" class="btn btn-sm btn-primary-custom">
					<i class="fas fa-user-plus"></i> Register
				</a>
				<a href="login/login.php" class="btn btn-sm btn-secondary-custom">
					<i class="fas fa-sign-in-alt"></i> Login
				</a>
			<?php endif; ?>
		</div>
	</div>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="container">
			<div class="row justify-content-center text-center">
				<div class="col-lg-10 hero-content">
					<h1 class="hero-title">
						<i class="fas fa-leaf me-3" style="color: var(--accent-yellow);"></i>
						Taste of Africa
					</h1>
					<p class="hero-subtitle">
						<i class="fas fa-star me-2"></i>
						Authentic Flavors, Unforgettable Experiences
					</p>
					
					<?php if (is_user_logged_in()): ?>
						<div class="hero-description">
							<i class="fas fa-heart" style="color: var(--accent-yellow);"></i>
							Welcome back, <strong><?php echo htmlspecialchars(get_user_name()); ?></strong>! 
							<?php if (is_user_admin()): ?>
								<span style="color: var(--accent-yellow);">You have administrative privileges.</span>
							<?php endif; ?>
							Ready to explore more delicious African cuisine?
						</div>
						<div class="mt-4">
							<?php if (is_user_admin()): ?>
								<a href="admin/dashboard.php" class="cta-button pulse-animation">
									<i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
								</a>
								<a href="admin/category.php" class="cta-button pulse-animation">
									<i class="fas fa-tags me-2"></i>Categories
								</a>
								<a href="admin/brand.php" class="cta-button pulse-animation">
									<i class="fas fa-copyright me-2"></i>Brands
								</a>
								<a href="admin/product.php" class="cta-button pulse-animation">
									<i class="fas fa-box-open me-2"></i>Add Product
								</a>
							<?php endif; ?>
							<a href="customer/order_food.php" class="cta-button <?php echo is_user_admin() ? 'cta-secondary' : 'pulse-animation'; ?>">
								<i class="fas fa-utensils me-2"></i>Explore Menu
							</a>
							<a href="customer/order_food.php" class="cta-button cta-secondary">
								<i class="fas fa-shopping-cart me-2"></i>Order Now
							</a>
						</div>
					<?php else: ?>
						<div class="hero-description">
							<i class="fas fa-globe-africa" style="color: var(--accent-yellow);"></i>
							Discover the rich, vibrant flavors of African cuisine. From spicy Ethiopian dishes to 
							savory West African delicacies, embark on a culinary journey like no other.
						</div>
						<div class="mt-4">
							<a href="login/register.php" class="cta-button pulse-animation">
								<i class="fas fa-user-plus me-2"></i>Join Our Family
							</a>
							<a href="login/login.php" class="cta-button cta-secondary">
								<i class="fas fa-sign-in-alt me-2"></i>Sign In
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="features-section">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 style="font-family: 'Playfair Display', serif; color: var(--text-brown); font-size: 2.5rem; margin-bottom: 1rem;">
						<i class="fas fa-fire" style="color: var(--primary-orange);"></i> Why Choose Us?
					</h2>
					<p style="color: var(--warm-brown); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
						Experience authentic African cuisine with modern convenience and exceptional service.
					</p>
				</div>
			</div>
			
			<div class="row g-4">
				<div class="col-md-4">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-pepper-hot"></i>
						</div>
						<h3 class="feature-title">Authentic Flavors</h3>
						<p class="feature-description">
							Traditional recipes passed down through generations, using authentic spices and cooking methods.
						</p>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-leaf"></i>
						</div>
						<h3 class="feature-title">Fresh Ingredients</h3>
						<p class="feature-description">
							Fresh, locally-sourced ingredients combined with imported African spices for the perfect taste.
						</p>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-shipping-fast"></i>
						</div>
						<h3 class="feature-title">Fast Delivery</h3>
						<p class="feature-description">
							Hot, delicious meals delivered to your doorstep in 30 minutes or less. Satisfaction guaranteed!
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Menu Preview Section -->
	<section id="menu" class="menu-preview-section" style="padding: 80px 0; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 style="font-family: 'Playfair Display', serif; color: var(--text-brown); font-size: 2.5rem; margin-bottom: 1rem;">
						<i class="fas fa-utensils" style="color: var(--primary-orange);"></i> Featured Dishes
					</h2>
					<p style="color: var(--warm-brown); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
						Discover our most popular African dishes, carefully crafted with authentic ingredients and traditional techniques.
					</p>
				</div>
			</div>
			
			<div class="row g-4">
				<div class="col-lg-4 col-md-6">
					<div class="menu-item-card" style="background: white; border-radius: 15px; padding: 2rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
						<div class="menu-item-image" style="width: 100%; height: 200px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
							<i class="fas fa-drumstick-bite"></i>
						</div>
						<h4 style="color: var(--text-brown); margin-bottom: 1rem;">Jollof Rice</h4>
						<p style="color: var(--warm-brown); margin-bottom: 1.5rem;">West Africa's most beloved rice dish, cooked with tomatoes, onions, and aromatic spices.</p>
						<div class="price" style="font-size: 1.5rem; font-weight: bold; color: var(--primary-orange);">$12.99</div>
					</div>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<div class="menu-item-card" style="background: white; border-radius: 15px; padding: 2rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
						<div class="menu-item-image" style="width: 100%; height: 200px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
							<i class="fas fa-fish"></i>
						</div>
						<h4 style="color: var(--text-brown); margin-bottom: 1rem;">Injera with Doro Wat</h4>
						<p style="color: var(--warm-brown); margin-bottom: 1.5rem;">Ethiopian sourdough flatbread served with spicy chicken stew and vegetables.</p>
						<div class="price" style="font-size: 1.5rem; font-weight: bold; color: var(--primary-orange);">$15.99</div>
					</div>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<div class="menu-item-card" style="background: white; border-radius: 15px; padding: 2rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
						<div class="menu-item-image" style="width: 100%; height: 200px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
							<i class="fas fa-hamburger"></i>
						</div>
						<h4 style="color: var(--text-brown); margin-bottom: 1rem;">Bunny Chow</h4>
						<p style="color: var(--warm-brown); margin-bottom: 1.5rem;">South African curry served in a hollowed-out bread loaf, a true comfort food classic.</p>
						<div class="price" style="font-size: 1.5rem; font-weight: bold; color: var(--primary-orange);">$13.99</div>
					</div>
				</div>
			</div>
			
			<div class="text-center mt-5">
				<a href="customer/order_food.php" class="cta-button pulse-animation">
					<i class="fas fa-utensils me-2"></i>View Full Menu
				</a>
			</div>
		</div>
	</section>

	<!-- Statistics Section -->
	<section class="stats-section" style="padding: 60px 0; background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-yellow) 100%); color: white;">
		<div class="container">
			<div class="row text-center">
				<div class="col-md-3 col-6 mb-4">
					<div class="stat-item">
						<div class="stat-number" style="font-size: 3rem; font-weight: bold; margin-bottom: 0.5rem;">500+</div>
						<div class="stat-label" style="font-size: 1.1rem; opacity: 0.9;">Happy Customers</div>
					</div>
				</div>
				<div class="col-md-3 col-6 mb-4">
					<div class="stat-item">
						<div class="stat-number" style="font-size: 3rem; font-weight: bold; margin-bottom: 0.5rem;">50+</div>
						<div class="stat-label" style="font-size: 1.1rem; opacity: 0.9;">Authentic Dishes</div>
					</div>
				</div>
				<div class="col-md-3 col-6 mb-4">
					<div class="stat-item">
						<div class="stat-number" style="font-size: 3rem; font-weight: bold; margin-bottom: 0.5rem;">15+</div>
						<div class="stat-label" style="font-size: 1.1rem; opacity: 0.9;">African Countries</div>
					</div>
				</div>
				<div class="col-md-3 col-6 mb-4">
					<div class="stat-item">
						<div class="stat-number" style="font-size: 3rem; font-weight: bold; margin-bottom: 0.5rem;">30min</div>
						<div class="stat-label" style="font-size: 1.1rem; opacity: 0.9;">Avg Delivery Time</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Testimonials Section -->
	<section class="testimonials-section" style="padding: 80px 0; background: var(--cream-white);">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 style="font-family: 'Playfair Display', serif; color: var(--text-brown); font-size: 2.5rem; margin-bottom: 1rem;">
						<i class="fas fa-quote-left" style="color: var(--primary-orange);"></i> What Our Customers Say
					</h2>
					<p style="color: var(--warm-brown); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
						Don't just take our word for it - hear from our satisfied customers.
					</p>
				</div>
			</div>
			
			<div class="row g-4">
				<div class="col-lg-4 col-md-6">
					<div class="testimonial-card" style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
						<div class="testimonial-avatar" style="width: 80px; height: 80px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
							<i class="fas fa-user"></i>
						</div>
						<div class="testimonial-text" style="color: var(--warm-brown); font-style: italic; margin-bottom: 1.5rem; line-height: 1.6;">
							"The Jollof rice here is absolutely amazing! It reminds me of home. The flavors are authentic and the service is excellent."
						</div>
						<div class="testimonial-author" style="font-weight: bold; color: var(--text-brown);">Sarah Johnson</div>
						<div class="testimonial-rating" style="color: var(--accent-yellow); margin-top: 0.5rem;">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<div class="testimonial-card" style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
						<div class="testimonial-avatar" style="width: 80px; height: 80px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
							<i class="fas fa-user"></i>
						</div>
						<div class="testimonial-text" style="color: var(--warm-brown); font-style: italic; margin-bottom: 1.5rem; line-height: 1.6;">
							"I've been looking for authentic African food in this city for years. Finally found it here! The Injera is perfect."
						</div>
						<div class="testimonial-author" style="font-weight: bold; color: var(--text-brown);">Michael Chen</div>
						<div class="testimonial-rating" style="color: var(--accent-yellow); margin-top: 0.5rem;">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<div class="testimonial-card" style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
						<div class="testimonial-avatar" style="width: 80px; height: 80px; background: linear-gradient(135deg, #e67e22, #f39c12); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
							<i class="fas fa-user"></i>
						</div>
						<div class="testimonial-text" style="color: var(--warm-brown); font-style: italic; margin-bottom: 1.5rem; line-height: 1.6;">
							"Fast delivery, fresh ingredients, and incredible taste. This is now my go-to place for African cuisine!"
						</div>
						<div class="testimonial-author" style="font-weight: bold; color: var(--text-brown);">Aisha Okafor</div>
						<div class="testimonial-rating" style="color: var(--accent-yellow); margin-top: 0.5rem;">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Call to Action Section -->
	<section id="order" class="cta-section" style="padding: 80px 0; background: linear-gradient(135deg, var(--text-brown) 0%, var(--dark-brown) 100%); color: white; text-align: center;">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 1.5rem;">
						<i class="fas fa-heart" style="color: var(--accent-yellow);"></i> Ready to Experience Authentic African Cuisine?
					</h2>
					<p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
						Join thousands of satisfied customers who have discovered the rich flavors of Africa. 
						Order now and taste the difference!
					</p>
					<div class="cta-buttons">
						<?php if (is_user_logged_in()): ?>
							<a href="customer/order_food.php" class="cta-button pulse-animation" style="margin: 0 10px;">
								<i class="fas fa-shopping-cart me-2"></i>Start Ordering
							</a>
							<a href="customer/customer_dashboard.php" class="cta-button cta-secondary" style="margin: 0 10px;">
								<i class="fas fa-tachometer-alt me-2"></i>My Dashboard
							</a>
						<?php else: ?>
							<a href="login/register.php" class="cta-button pulse-animation" style="margin: 0 10px;">
								<i class="fas fa-user-plus me-2"></i>Join Our Family
							</a>
							<a href="login/login.php" class="cta-button cta-secondary" style="margin: 0 10px;">
								<i class="fas fa-sign-in-alt me-2"></i>Sign In
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer" style="background: var(--dark-brown); color: var(--cream-white); padding: 40px 0; text-align: center;">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h5 style="color: var(--accent-yellow); margin-bottom: 1rem;">
						<i class="fas fa-leaf me-2"></i>Taste of Africa
					</h5>
					<p style="opacity: 0.8; margin-bottom: 1rem;">
						Bringing authentic African flavors to your doorstep. Experience the rich culinary heritage of Africa.
					</p>
				</div>
				<div class="col-md-6">
					<h6 style="color: var(--accent-yellow); margin-bottom: 1rem;">Quick Links</h6>
					<div class="footer-links">
						<?php if (is_user_logged_in()): ?>
							<a href="customer/order_food.php" style="color: var(--cream-white); text-decoration: none; margin-right: 15px;">Menu</a>
							<a href="customer/customer_dashboard.php" style="color: var(--cream-white); text-decoration: none; margin-right: 15px;">Dashboard</a>
							<a href="customer/my_orders.php" style="color: var(--cream-white); text-decoration: none; margin-right: 15px;">My Orders</a>
						<?php else: ?>
							<a href="login/login.php" style="color: var(--cream-white); text-decoration: none; margin-right: 15px;">Login</a>
							<a href="login/register.php" style="color: var(--cream-white); text-decoration: none; margin-right: 15px;">Register</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<hr style="border-color: var(--primary-orange); margin: 2rem 0;">
			<div class="row">
				<div class="col-12">
					<p style="opacity: 0.7; margin: 0;">
						&copy; 2024 Taste of Africa. All rights reserved. | Authentic African Cuisine Delivered
					</p>
				</div>
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Add hover effects to menu item cards
		document.addEventListener('DOMContentLoaded', function() {
			const menuCards = document.querySelectorAll('.menu-item-card');
			menuCards.forEach(card => {
				card.addEventListener('mouseenter', function() {
					this.style.transform = 'translateY(-10px)';
				});
				card.addEventListener('mouseleave', function() {
					this.style.transform = 'translateY(0)';
				});
			});
		});
	</script>
</body>
</html>