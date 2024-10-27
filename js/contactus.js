window.onload = function() {
    var conversationalForm = window.cf.ConversationalForm.startTheConversation({
        formEl: document.getElementById("form"),
        context: document.getElementById("cf-context"),
        userImage: php_vars.plugin_url + "/images/boy.png",
        robotImage: php_vars.plugin_url + "/images/girl.jpeg",
        submitCallback: function() {
            jQuery.ajax({
                type: "POST",
                url: php_vars.contactus_ajaxurl+"?action=qdcu_send_email",
                data: jQuery("form#cf-form").serialize(),
                beforeSend: function () {

                },
                success: function (Return) {
                    if(Return == 1)
                        conversationalForm.addRobotChatResponse(php_vars.success_message);
                    else
                        conversationalForm.addRobotChatResponse(php_vars.error_message);
                }
            });
        }
    });
};
