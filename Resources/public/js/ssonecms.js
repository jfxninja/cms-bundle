/////////////////////////////////////////////////////////////////////////////////
//Content Type Add/Remove
//////////////////////////////////////////////////////////////////////////////////

var $collectionHolder;

// setup an "add a field" link;
var $newLinkLi = '<li class="ui-non-sortable"><a href="" class="tiny button add_property_link">+</a></li>';

jQuery(document).ready(function() {

    // add a delete link to all of the existing field form li elements
    $('dd.properties').each(function() {
        addPropertyFormDeleteLink($(this));
    })

    // add the "add a property" anchor and li to the properties ul
    $('dl.properties').each(function() {
        $(this).append($newLinkLi);
    })


    $('.add_property_link').click(function(e) {

        // Get the ul that holds the collection of fields
        $collectionHolder = $(this).closest('dl');

        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addPropertyForm($collectionHolder, $(this).parent());


    });
});

function addPropertyForm($collectionHolder, $newLinkLi) {


    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.children().length);

    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var formEncased = prototype.replace(/__name__label__/g, "");
    var formEncased = formEncased.replace(/__name__/g, index);
    var formEncased = formEncased.replace(/__index__/g, index);
    var $formEncasedobject = $('<div/>').html(formEncased);
    var newForm = $formEncasedobject.contents();

    console.log(newForm);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    var nextFieldIndex = $('dd.properties').length +1;

    // Display the form in the page in an li, before the "Add a property" link li
    var $newFormLi = $('<dd class="properties row" id="field-'+nextFieldIndex+'"></dd>').append(newForm);
    addPropertyFormDeleteLink($newFormLi);
    $newLinkLi.before($newFormLi);

    hideShowSelectedSettings('field-'+nextFieldIndex);
    bindFieldTypeChange('field-'+nextFieldIndex);

    setSort($collectionHolder.children());

}

function addPropertyFormDeleteLink($propertyFormLi) {
    var $removeFormA = $('<div class="row column"><div class="item-actions column small-3"><a class="remove" href="#">Remove</a> | <span class="handle">Drag</span></div></div>');
    $propertyFormLi.append($removeFormA);

    $removeFormA.children().children(".remove").on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the property from the form
        $propertyFormLi.remove();
    });



}


/////////////////////////////////////////////////////////////////////////////////
//Hide/Show field type setup options
//////////////////////////////////////////////////////////////////////////////////

jQuery(document).ready(function() {

    // select all fields
    $('.row.field-type-settings').hide();
    // hide all options
    // .each fields
    $('dd.properties').each(function(){
        var propertyGroupID = $(this).attr('id');
        hideShowSelectedSettings(propertyGroupID);
    });

    $('.properties').each(function(){
        var liID = $(this).attr('id');
        bindFieldTypeChange(liID);

    });

});

function hideShowSelectedSettings(propertyGroupID)
{
    var selectVal =  $('#'+propertyGroupID+' .field-type').val();
    $('#'+propertyGroupID+' .row.field-type-settings').hide();
    $('#'+propertyGroupID+' .row.field-type-settings.group-'+selectVal).show();
}

function bindFieldTypeChange(propertyGroupID)
{
    $('#'+propertyGroupID+' .field-type').change(function(){
        $('#'+propertyGroupID+' .row.field-type-settings').hide();
        $('#'+propertyGroupID+' .row.field-type-settings.group-'+$(this).val()).show();

    });
}

/////////////////////////////////////////////////////////////////////////////////
//Sortable fields
//////////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {

    $('dl.ui-sortable').each(function(){
        setSort($(this).children());
    });

    $( "dl.ui-sortable" ).sortable({
        handle: '.handle',
        placeholder: "ui-state-highlight",
        items: "dd:not(.ui-non-sortable)",
        update: function( event, ui ) {
            setSort($(this).children());
        }
    });

});

function setSort(nodes)
{

    nodes.each(function(index){
        $(this).find('.sort').attr('value',index);

    });

}


/////////////////////////////////////////////////////////////////////////////////
//Menu Items, ContentType category fields requests
//////////////////////////////////////////////////////////////////////////////////


$(document).ready(function() {
    $('#menuItem_mapContent').on('change', function(){
        getContentCatgoeries($(this).val(),setContentCategories);
    });

    $('#menuItem_contentCategory2').on('change', function(){
        getContentCategoryRelatedFields($('#menuItem_contentCategory2').val(),setContentCategoryRelatedFields);
    });
});



function getContentCatgoeries(mt,callback) {
    $.ajax({
        url: '/admin/menu-items/ajax/contentTypeCategories',
        data: {menuType: mt},
        type: "POST",
        success: callback

    });
}

function getContentCategoryRelatedFields(category2FieldId,callback) {
    $.ajax({
        url: '/admin/menu-items/ajax/ContentTypeCategoryRelatedFields',
        data: {category2FieldId: category2FieldId},
        type: "POST",
        success: callback

    });
}


function setContentCategories(results)
{
    appendULOptionsFieldnameValue($('#menuItem_contentCategory1'),results.data);
    appendULOptionsFieldnameValue($('#menuItem_contentCategory2'),results.data);

}

function setContentCategoryRelatedFields(results)
{
    appendULOptionsFieldnameValue($('#menuItem_contentCategoryRelationship'),results.data);

}


function appendULOptionsFieldnameValue($ul,options) {

    $ul.empty();

    if(options.length)
    {
        var defaultText = "Choose an option";
    }
    else
    {
        var defaultText = "N/A";
    }

    $ul.append(
        $("<option>")
            .val("")
            .html(defaultText)
    );

    for (var option in options)
    {
        $ul.append(
            $("<option>")
                .val(options[option]['id'])
                .html(options[option]['name'])
        );
    }
}


/////////////////////////////////////////////////////////////////////////////////
// Dynamic module settings
//////////////////////////////////////////////////////////////////////////////////


//Change contentType
$(document).ready(function() {
    $('#module_contentType').on('change', function(){
        getModuleContentDisplayOptions($(this).val(),setModuleContentDisplayOptions);
    });

    $('#module_contentFilterField').on('change', function(){
        getModuleFilterValueOptions($(this).val(),setModuleFilterValueOptions);
    });

});

function getModuleContentDisplayOptions(ct,callback) {
    $.ajax({
        url: '/admin/modules/ajax/moduleContentDisplayOptions',
        data: {contentTypeId: ct},
        type: "POST",
        success: callback

    });
}

function setModuleContentDisplayOptions(results)
{

    options = new Array();

    console.log(results);
    //Update choose single item options - set to content list items
    appendULOptionsFieldnameValue($('#module_singleContentItem'),results.contentItemChoices);
    //Update filterByRelated content - set to content type fields
    appendULOptionsKeyValue($('#module_contentFilterField'),results.filterFieldOptions);
    //Update content filtervalue - set blank
    appendULOptionsKeyValue($('#module_contentFilterValue'),options);
    //Update content orderby fields
    appendULOptionsKeyValue($('#module_contentOrderByField'),results.orderbyFieldOptions);

}

function getModuleFilterValueOptions(fid,callback) {
    $.ajax({
        url: '/admin/modules/ajax/getModuleFilterValueOptions',
        data: {contentTypeId: fid},
        type: "POST",
        success: callback
    });
}


function setModuleFilterValueOptions(results)
{


    console.log(results);
    if(results.data == "text")
    {
        $('#module_contentFilterValue').replaceWith('<input type="text" id="module_contentFilterValue" name="module[contentFilterValue]" required="required">');
    }
    else
    {
        $('#module_contentFilterValue').replaceWith('<select id="module_contentFilterValue" name="module[contentFilterValue]"></select>');
    }
    appendULOptionsKeyValue($('#module_contentFilterValue'),results.data);

}


function appendULOptionsKeyValue($ul,options) {

    $ul.empty();

    if(Object.keys(options).length)
    {
        var defaultText = "Choose an option";
    }
    else
    {
        var defaultText = "N/A";
    }

    $ul.append(
        $("<option>")
            .val("")
            .html(defaultText)
    );

    for (var option in options)
    {
        $ul.append(
            $("<option>")
                .val(option)
                .html(options[option])
        );
    }
}









































