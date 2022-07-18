    $(document).ready(function() {

      
   $('#form-modal-status-store').bootstrapValidator({
                   // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
                   
               fields: {
                   reply: {
                       validators: {
                           notEmpty: {
                               message: 'Please enter your reply'
                           },  
                           stringLength: {
                            min: 3,                  
                            max: 55,
                            message: 'The reason should contain 3 to 55 characters'
                        },                              
                                                                   
                       }
                   },
               }
           });

    /////////////////////////////////////////////admin////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////


    

        
    $('#dashboard_form').bootstrapValidator({
        fields: {
            active_from: {
                validators: {
                    notEmpty: {
                        message: 'Please select a date'
                    }, 
                },
            },
            active_to: {
                validators: {
                    notEmpty: {
                        message: 'Please  select a date'
                    }, 
                },
            }
        }
    
    });
    $('#banner').bootstrapValidator({
    fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter name'
                    }, 
                    stringLength: {
                        min: 3,                  
                        max: 55,
                        message: 'The name should contain 3 to 55 characters'
                    },                
                },                            
                    
            },
            action_name: {
                validators: {
                    stringLength: {
                        min: 3,                  
                        max: 155,
                        message: 'The action name should contain 3 to 155 characters'
                    },                
                },                            
                    
            },
            action_url: {
                validators: {
                    stringLength: {
                        min: 3,                  
                        max: 155,
                        message: 'The action url should contain 3 to 155 characters'
                    },                
                },                                  
            },
            description: {
                validators: {
                   
                    stringLength: {
                        min: 3,                  
                        max: 155,
                        message: 'The description should contain 3 to 155 characters'
                    },                
                },                            
                    
            },
           
            photo: {
                validators: {
                    notEmpty: {
                        message: 'Please select photo.'
                    },                 
                    file: {
                        extension: 'jpg,png,jpeg',
                        type: 'image/jpeg,image/jpg,image/png',
                        maxSize: 2048 * 1024,
                        message: 'The selected file is not valid'
                    },                 
                },                            
                    
            },
        },
    });

    $('#admincreate').bootstrapValidator({
        fields: {
            fullname: {
                validators: {
                    notEmpty: {
                        message: 'Please enter your full name.'
                    },
                    stringLength: {
                        min: 3,
                        max: 55,
                        message: 'The full name should between 3 to 55 characters long.'
                    },
                    
                },
            }, 
            username: {
                validators: {
                    notEmpty: {
                        message: 'Please enter your username.'
                    },
                    stringLength: {
                        min: 3,
                        max: 55,
                        message: 'The user name should between 3 to 55 characters long.'
                    },
                    
                },
            }, 
           
            email: {
                validators: {
                    notEmpty: {
                        message: 'Please enter your E-mail.'
                    },
                  
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: 'The value is not a valid email address.'
                    }
                },
            },
            cellphone: {
                validators: {
                     notEmpty: {
                        message: 'Please enter your cellphone number.'
                    },
                   
                    stringLength: {
                        min: 7,
                        max: 15,
                        message: 'The number should contain 7 to 15 digit.'
                    },
                },
            },
            user_type:{
                validators: {
                    notEmpty: {
                       message: 'Please enter user Profile Type.'
                   },
                },
            }  ,     
            password: {
                validators: {
                    notEmpty: {
                        message: 'Please enter your password.'
                    },
                    /*stringLength: {
                    min: 6,
                    max: 16,
                    message: 'The password should contain 6 to 16 characters'
                    },*/
                    regexp: {
                          regexp: "^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&-+=()])(?=\\S+$).{8,}$",

                            message: 'Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character.'
                       } 
                }
            },
            password_confirmation: {
                validators: {
                    notEmpty: {
                        message: 'Please enter password confirmation.'
                    },
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same.'
                    }
                }
            },           
           
            
        }    
    });

    // $('#admin-profile').bootstrapValidator({
    //     fields: {
    //         fullname: {
    //             validators: {
    //                 notEmpty: {
    //                     message: ' Please enter your full name'
    //                 },
    //                 stringLength: {
    //                     min: 3,
    //                     max: 55,
    //                     message: 'The full name should between 3 to 55 characters long'
    //                 },
                    
    //             },
    //         }, 
        
        
    //         cellphone: {
    //             validators: {
                  
    //                 integer : { 
    //                     message : 'Please enter the valid number ',                    
    //                     noSpace:true
    //                 },
                   
    //                 stringLength: {
    //                     min: 7,
    //                     max: 15,
    //                     message: 'The number should contain 7 to 15 digit'
    //                 },
    //             },
    //         },
          
    //         password: {
    //             validators: {
                   
    //                 /*stringLength: {
    //                 min: 6,
    //                 max: 16,
    //                 message: 'The password should contain 6 to 16 characters'
    //                 },*/
    //                 regexp: {
    //                       regexp: "^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&-+=()])(?=\\S+$).{8,}$",

    //                         message: 'Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character.'
    //                    } 
    //             }
    //         },
    //         password_confirmation: {
    //             validators: {
                    
    //                 identical: {
    //                     field: 'password',
    //                     message: 'The password and its confirm are not the same'
    //                 }
    //             }
    //         },           
           
            
    //     }    
    // });

    $('#stores').bootstrapValidator({
        fields: {
            shop_name: {
               validators: {
                   notEmpty: {
                       message: 'Please enter store name'
                   }, 
                   stringLength: {
                       min: 3,                  
                       max: 55,
                       message: 'The store name should contain 3 to 55 characters'
                   },                
               },                            
                
           },
           shop_description: {
               validators: {
                   notEmpty: {
                       message: 'Please enter shop bio'
                   }, 
                   stringLength: {
                       min: 3,                  
                       max: 55,
                       message: 'The shop bio should contain 3 to 55 characters'
                   },                
               },                            
                
           },
           shop_address: {
            validators: {
                notEmpty: {
                    message: 'Please enter shop address'
                }, 
                stringLength: {
                    min: 3,                  
                    max: 55,
                    message: 'The shop address  should contain 3 to 155 characters'
                },                
            },                            
             
        },
           shop_mobile: {
            validators: {
                 
                integer : { 
                    message : 'Please enter the valid number ',                    
                    noSpace:true
                },
               
                stringLength: {
                    min: 7,
                    max: 15,
                    message: 'The number should contain 7 to 15 digit'
                },
            },
        },
           
         
    },
});
$('#category').bootstrapValidator({
    fields: {
       name: {
           validators: {
               notEmpty: {
                   message: 'Please enter name'
               },
               stringLength: {
                min: 3,                  
                max: 55,
                message: 'The name should contain 3 to 55 characters'
            },   
           },
       }
   }
});

    $('#form-edit').bootstrapValidator({
        fields: {
           icon: {
               validators: {
                   notEmpty: {
                       message: 'Please enter icon'
                   }, 
                   stringLength: {
                       min: 3,                  
                       max: 55,
                       message: 'The icon should contain 3 to 55 characters'
                   },                
               },                            
                
           },
           name: {
               validators: {
                   notEmpty: {
                       message: 'Please enter name'
                   }, 
                   stringLength: {
                       min: 3,                  
                       max: 55,
                       message: 'The name should contain 3 to 55 characters'
                   },                
               },                            
                
           },

           description: {
               validators: {
                   notEmpty: {
                       message: 'Please enter description'
                   }, 
                   stringLength: {
                       min: 3,                  
                       max: 550,
                       message: 'The description should contain 3 to 550 characters'
                   },                
               },                            
                
           },
           
           
         
       },
   });


   $('#email').bootstrapValidator({
    fields: {
       name: {
           validators: {
               notEmpty: {
                   message: 'Please enter name'
               }, 
               stringLength: {
                   min: 3,                  
                   max: 55,
                   message: 'The name should contain 3 to 55 characters'
               },                
           },                            
            
       },
       action: {
           validators: {
               notEmpty: {
                   message: 'Please select action'
               }, 
                            
           },                            
            
       },


       subject: {
           validators: {
               notEmpty: {
                   message: 'Please enter subject'
               }, 
               stringLength: {
                   min: 3,                  
                   max: 55,
                   message: 'The subject should contain 3 to 55 characters'
               },                
           },                            
            
       },

   },
});
});