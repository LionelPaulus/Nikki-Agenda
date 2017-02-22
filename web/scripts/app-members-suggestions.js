$('.members-input').textext({
	plugins : 'tags prompt focus autocomplete ajax arrow',
	prompt : 'Add one...',
	ajax : {
		url : 'app/getMembersSuggestions.json',
		dataType : 'json',
		cacheResults : true,
        typeDelay : 1,
        loadingDelay: 99999999
	}
});
