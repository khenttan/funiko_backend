$(document).ready(function() {
    jQuery.validator.addMethod("regPassword", function(value, element) {
        // allow any non-whitespace characters as the host part
        return this.optional( element ) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test( value );
      }, 'Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character.');

    $("#LoginForm").validate({
      rules: {
       
        email: {
          required: true,
          email: true
        },
        password: {
            required: true,
        },
      },
      messages : {
        
        email: {
            required: "Please enter email",
        
            email: "The email should be in a valid format."
        
        },
        password: {
          required: "Please enter password",
        }
      }
    });
    $("#emailForm").validate({
        rules: {
         
          email: {
            required: true,
            email: true
          },
        
        },
        messages : {
          
          email: {
              required: "Please enter email",
          
              email: "The email should be in a valid format."
          
          },
        
        }
      });
    $("#resetPassword").validate({
        rules : {
            password : {
                required: true,
                regPassword : true,
            },
            password_confirmation : {
                required: true,
                equalTo : "#input-clave"
            }
        },
        messages : {
          
            password: {
                required: "Please enter password",
            },
            password_confirmation: {
                required: "Please enter confirm password",
            
                
            },
          
          }
    });

  });

  