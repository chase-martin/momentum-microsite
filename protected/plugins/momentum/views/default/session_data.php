<script>
<?php
    echo "var firstname='" . $this->registry->session->_ds_firstname . "';\n";
    echo "var lastname='" . $this->registry->session->_ds_lastname . "';\n";
    echo "var company='" . $this->registry->session->_ds_company . "';\n";
    echo "var phone='" . $this->registry->session->_ds_phone . "';\n";
    echo "var email='" . $this->registry->session->_ds_email . "';\n";
    echo "var eventbrite_url='" . $this->registry->session->_ds_eventbrite_url . "';\n";
?>
    $(document).ready(function () {
        $('#firstname').val(firstname);
        $('#lastname').val(lastname);
        $('#company').val(company);
        $('#email').val(email);
        if(eventbrite_url != '') {
            var eblink = $(".eventbrite-register-href").attr('href');
            $(".eventbrite-register-href").attr('href', eblink + eventbrite_url);
        }
    });
</script>
