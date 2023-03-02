<script>
	jQuery('tr[data-revisor="<? echo $this->name; ?>"').each( function(){
		const id = parseInt( jQuery(this).find('td')[0].innerHTML )
		jQuery(`tr[data-id="${id}"]`).addClass('reviewed')
	})

	jQuery('.toggle-reviewed').click( function(e){
		jQuery('.reviewed').toggle()
	})
</script>
