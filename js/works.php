<script>
jQuery('.open-work').click( function(e){
    const id = jQuery(this).attr('data-id')
    jQuery('#work-content').html('')

    fetch("https://campus.aptec.events/wp-admin/admin-ajax.php?action=show_detailed_submission&id=" + id)
        .then((response) => response.text())
        .then((data) => {
            jQuery('#work-content').html(data)
            document.querySelector('#work-content').scrollIntoView()
        })
})
</script>