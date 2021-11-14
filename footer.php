<?php
?>
<footer class="site-footer">
	<hr>
    <div class="flex justify-space-between">
        <div class="flex-item">
            <a href="<?php echo esc_url(home_url('/')); ?>"
            rel="home"><?php bloginfo('name'); ?></a>
        </div>
        <div class="flex-item">
            Proudly powered by <a href="https://wordpress.com/">WordPress</a>
        </div>
    </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>

</body>
</html>
