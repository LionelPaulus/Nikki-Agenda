$('.members-input').textext({
	plugins : 'tags prompt focus autocomplete ajax arrow',
	prompt : 'Type an email...',
	ajax : {
		url : 'api/getMembersSuggestions',
		dataType : 'json',
		cacheResults : true,
        typeDelay : 1,
        loadingDelay: 99999999
	}
});
