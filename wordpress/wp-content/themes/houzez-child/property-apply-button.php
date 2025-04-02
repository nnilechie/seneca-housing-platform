<?php
if (is_singular('property')) {
    echo '<a href="' . site_url('/rental-application-form/?property_id=' . get_the_ID()) . '" class="btn btn-primary">Apply Now</a>';
}
?>
