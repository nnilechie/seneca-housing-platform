jQuery(document).ready(function($) {
    // Conditional fields based on user type
    $('#user-type').change(function() {
        const userType = $(this).val();
        let fields = '';
        if (userType === 'employed') {
            fields = '<label>Employer Name: <input type="text" name="employer_name"></label>';
        } else if (userType === 'student') {
            fields = '<label>Student ID: <input type="text" name="student_id"></label>';
        } else if (userType === 'expat') {
            fields = '<label>Visa Status: <input type="text" name="visa_status"></label>';
        }
        $('#conditional-fields').html(fields);
    });

    // Show/hide form on "Apply Now" click
    $('.hra-apply-now').click(function() {
        $(this).next('.hra-application-form').toggle();
    });
});