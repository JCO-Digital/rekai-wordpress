<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<h2 id="overriding-test-mode">Overriding Test Mode</h2>
<p>
    If test mode is enabled, Rek.ai will not send any data to Rek.ai. This is useful for testing and development.
    This is determined by the environment type first, if it is not production, then test mode is always enabled.
    Otherwise, test mode is determined by the test mode setting.
</p>
<p>
    However, it can be overridden by the <code>rekai_override_test_mode</code> filter.
</p>
<p>
    E.g., to always disable test mode, add the following to your theme's functions.php file:
</p>
<pre>
    <code class="language-php">
        add_filter( 'rekai_override_test_mode', '__return_false' );

        // However, the current WP environment type and the current test mode setting is also passed as parameters to the filter.
        add_filter( 'rekai_override_test_mode', 'rekai_arguments_test', 10, 3);
        function rekai_arguments_test( $override, $env_type, $test_mode ) {
            // Env type will be the result of `wp_get_environment_type()`.
            // And $test_mode will be the result of the test mode setting.
            return true;
        }
    </code>
</pre>
