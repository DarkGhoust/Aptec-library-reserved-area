<script>
jQuery('.open-mark').click( function(e){
    const id = jQuery(this).attr('data-id')
    jQuery('#mark-content').html('')

    fetch("https://campus.aptec.events/wp-admin/admin-ajax.php?action=show_detailed_submission&id=" + id)
        .then((response) => response.text())
        .then((data) => {
            jQuery('#mark-content').html(data)
            document.querySelector('#mark-content').scrollIntoView()
        })
})
</script>