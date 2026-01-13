$(document).ready(function() {
    // Handle Hero Form Submission
    $('#contactFormHero').on('submit', function(e) {
        e.preventDefault();
        
        // Get reCAPTCHA response
        var recaptchaResponse = grecaptcha.getResponse();
        
        if (!recaptchaResponse) {
            alert('Please complete the reCAPTCHA verification');
            return;
        }
        
        // Add reCAPTCHA response to form data
        var formData = $(this).serialize() + '&g-recaptcha-response=' + recaptchaResponse;
        
        $.ajax({
            url: 'https://api.astraresults.com/send_email/v1/green-air',
            // url: 'http://localhost:4848/send_email/v1/green-air',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(result) {
                console.log('=== AJAX SUCCESS (Hero Form) ===');
                console.log('Full result:', result);
                
                if (result.success) {
                    console.log('Redirecting to thank-you.html');
                    window.location.href = 'thank-you.html';
                } else {
                    console.log('Redirecting to form-error.html');
                    window.location.href = 'form-error.html';
                }
            },
            error: function(xhr) {
                console.log('=== AJAX ERROR (Hero Form) ===');
                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText);
                window.location.href = 'form-error.html';
            }
        });
    });
});
