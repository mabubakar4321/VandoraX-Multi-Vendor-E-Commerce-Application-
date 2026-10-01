<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>For Customers</h3>
            <ul>
                <li><a href="{{ route('storefront.products.index') }}">Browse Products</a></li>
                <li><a href="{{ route('customer.register') }}">Create Account</a></li>
                <li><a href="#">Shipping Info</a></li>
                <li><a href="#">Return Policy</a></li>
                <li><a href="#">Track Orders</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>For Sellers</h3>
            <ul>
                <li><a href="{{ route('vendor.register') }}">Start Selling</a></li>
                <li><a href="{{ route('vendor.login') }}">Seller Login</a></li>
                <li><a href="#">Seller Guide</a></li>
                <li><a href="#">Commission Info</a></li>
                <li><a href="#">Support</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Company</h3>
            <ul>
                <li><a href="#">About VandoraX</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Connect</h3>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">LinkedIn</a></li>
                <li><a href="#">YouTube</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} VandoraX Premium Marketplace. All rights reserved. ✨</p>
    </div>
</footer>

<style>
    footer {
        background: var(--primary);
        padding: 80px 20px 40px;
        border-top: 1px solid var(--border-light);
    }

    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 50px;
        margin-bottom: 60px;
    }

    .footer-section h3 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 24px;
        color: var(--accent);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .footer-section ul {
        list-style: none;
    }

    .footer-section li {
        margin-bottom: 12px;
    }

    .footer-section a {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
        letter-spacing: 0.3px;
    }

    .footer-section a:hover {
        color: var(--accent);
        padding-left: 6px;
    }

    .footer-bottom {
        border-top: 1px solid var(--border-light);
        padding-top: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
        letter-spacing: 0.5px;
    }
</style>
