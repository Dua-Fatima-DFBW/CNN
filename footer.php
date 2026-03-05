<footer>
    <div class="footer-content">
        <div class="footer-brand">
            <h2>CNN NEWS</h2>
            <p style="color: #888; max-width: 300px;">
                Leading the world in breaking news coverage. Delivering the truth with speed and accuracy. Experience
                the future of news with our state-of-the-art digital platform.
            </p>
        </div>

        <div class="footer-links">
            <div class="footer-col">
                <h4>World</h4>
                <ul>
                    <li><a href="#">Africa</a></li>
                    <li><a href="#">Americas</a></li>
                    <li><a href="#">Asia</a></li>
                    <li><a href="#">Europe</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Tech & Business</h4>
                <ul>
                    <li><a href="#">Innovations</a></li>
                    <li><a href="#">Startups</a></li>
                    <li><a href="#">Markets</a></li>
                    <li><a href="#">Crypto</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Connect</h4>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Newsletter</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="copyright">
        &copy;
        <?php echo date("Y"); ?> CNN News Corporation. All Rights Reserved.
    </div>
</footer>

<!-- Simple JS for UI interactions if needed -->
<script>
    // Example: Add active class to current nav item
    const currentLocation = location.href;
    const menuItem = document.querySelectorAll('.nav-links a');
    const menuLength = menuItem.length;
    for (let i = 0; i < menuLength; i++) {
        if (menuItem[i].href === currentLocation) {
            menuItem[i].className = "active";
        }
    }
</script>

</body>

</html>
