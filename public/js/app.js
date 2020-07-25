$(() => {
    const $collectionHolder = $('div.videos')
    const $addButton = $('<button class="btn btn-primary">Ajouter une video</button>')
    
    $collectionHolder.after($addButton)

    function addTagForm($collectionHolder) {
        const index = $collectionHolder.data('index')
        const newForm = $collectionHolder.data('prototype').replace(/__name__/g, index)
    
        $collectionHolder.data('index', index + 1).append(newForm)
    }

    $collectionHolder.data('index', $collectionHolder.find('input').length)

    $addButton.on('click', () => { addTagForm($collectionHolder) })
})