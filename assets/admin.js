(($) => {
  $.fn.BlockConfigurator = function(color) {

    let _addOnIndex = -1;

    const _openSelection = function($list, event){
      let button = $(event.currentTarget);
      _addOnIndex = button.data('block-add-index');
      $list.find('[data-block-element="dialog-new"]')[0].showModal()
    }

    function uuidv4() {
      return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
        (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
      );
    }

    const _addItem = function($list, event){
          
      let button = $(event.currentTarget);
      let template = button.data('block-template-html');

      template = template.replaceAll(/\|--BLOCK-UID--\|/ig, uuidv4())
  
      const listItemsHolder = $list.find('[data-block-element="list"]');
  
      if(_addOnIndex == 'prepend'){
          listItemsHolder.prepend(template);
      }
      else if(_addOnIndex == 'append'){
          listItemsHolder.append(template);
      }
      else {
        $(template).insertAfter(listItemsHolder.children().eq(_addOnIndex));
      }
  
      $list.find('[data-block-element="dialog-new"]')[0].close()
  
      _afterChangeList($list);
    }

    const _move = function($list, event){
      let button = $(event.currentTarget);
      let $item = $(button.closest('li'));

      const direction = button.attr('data-block-direction');

      if(direction == 'up'){
        const prev = $item.prev();
        $item.insertBefore(prev);
      } else {
        const next = $item.next();
        $item.insertAfter(next);
      }

      _afterChangeList($list);
    }

    const _reindex = function($list) {

      const listItemsHolder = $list.find('[data-block-element="list"]');
      const items = listItemsHolder.find('> li');
  
      items.each((index, item) => {
        item = $(item);
        item[index == 0 ? 'addClass' : 'removeClass']('blk-first');
        item[index == (item.length -1) ? 'addClass' : 'removeClass']('blk-last');

        item.find('[data-block-add-index]').attr('data-block-add-index', index)
        item.find('[data-block-element="sort-field"]').val(index);
      });
    }

    const _toogleItem = function(event) {
      let button = $(event.currentTarget);
      let $item = $(button.closest('li'));


      if($item.hasClass('blk-small')){
        $item.removeClass('blk-small');
        button.removeClass('fa-chevron-circle-right').addClass('fa-chevron-circle-down');
        return;
      }
  
      button.removeClass('fa-chevron-circle-down').addClass('fa-chevron-circle-right');
      $item.addClass('blk-small');
    }

    let _removeElement = null;

    const _openRemoveDialog = function($list, event){
      let button = $(event.currentTarget);
      let $item = $(button.closest('li'));
      _removeElement = $item;
      $list.find('[data-block-element="dialog-remove"]')[0].showModal()
    }

    const _remove = function($list){
      _removeElement.remove();
      $list.find('[data-block-element="dialog-remove"]')[0].close()
      _afterChangeList($list);
    }

    const _afterChangeList = function($list){

      _reindex($list);
      const listItemsHolder = $list.find('[data-block-element="list"]');
  
      if(listItemsHolder.children().length == 0){
        $list.find(`[data-block-action="open-selection"][data-block-add-index="append"]`).addClass('hidden');
      } else {
        $list.find(`[data-block-action="open-selection"][data-block-add-index="append"]`).removeClass('hidden');
      }
    }

    return this.each(function() {
     
        const $list = $(this);
        
        if($list.attr('data-blocks-initialized')){
          return;
        }
        $list.attr('data-blocks-initialized', true);
        
        $list.on('click', '[data-block-action="open-selection"]', (event) => _openSelection($list, event));
        $list.on('click', `[data-block-action="add"]`, (event) => _addItem($list, event));
        $list.on('click', `[data-block-action="toggle"]`, (event) => _toogleItem(event));
        $list.on('click', `[data-block-action="open-remove-dialog"]`, (event) => _openRemoveDialog($list, event));
        $list.on('click', `[data-block-action="remove"]`, (event) => _remove($list, event));
        $list.on('click', `[data-block-action="move"]`, (event) => _move($list, event));
    });
  };

  const  _onAddedNodes = function(_, target) {
    $(target).find('[data-blocks]').each((_, list) => $(list).BlockConfigurator());
  }

  const _init = function(){
    const body = $("body");
    $('[data-blocks]').each((_, list) => $(list).BlockConfigurator());
    body.on("mutation._grav", _onAddedNodes);
  }

  _init();
})($);
