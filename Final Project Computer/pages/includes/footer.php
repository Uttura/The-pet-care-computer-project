    </main>
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Pet Care. All rights reserved.</p>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
    <style>
    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background: #f8f9fa;
        padding: 15px 0;
        text-align: center;
        border-top: 1px solid #eee;
        z-index: 999;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        color: #666;
        font-size: 0.9rem;
    }

    /* Add margin to main content to prevent footer overlap */
    main {
        margin-bottom: 60px; /* Adjust based on footer height */
    }
    </style>
</body>
</html> 