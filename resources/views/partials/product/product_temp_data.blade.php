<script>

    /*
        The following code takes all the text inputs to localstorage and updates it. Need to do this for other input types and select.
    */
    $(document).ready(function(){
        $('.page-content').find('input[type="text"]').on('blur',function(){
            storeTempdata($(this));
        });
        
        $('.page-content').find('input[type="number"]').on('blur',function(){
            storeTempdata($(this));
        });

        $('.page-content').find('select').on('change',function(){
            storeTempdata($(this));
        });

        $('.page-content').find('input[type="checkbox"]').on('change',function(){
            storeTempdata($(this));
        });

        $('.page-content').find('input[type="radio"]').on('change',function(){
            storeTempdata($(this));
        });

        $('.page-content').find('textarea').on('blur',function(){
            storeTempdata($(this));
        });
    });

    function storeTempdata(e) {
        var name = e.attr('name');
        var value = e.val();
        var data_type = $('#data_type').val();
        var category_ids = new Array();
        var tempData = localStorage.getItem('tempdataproduct_'+data_type);
        if(tempData == null){
            tempData = '{}';
        }
        var obj = JSON.parse(tempData);
        if (e.attr('type') == 'checkbox') {
            if (name == 'category_ids[]') {
                $("input:checkbox[name='category_ids[]']:checked").each(function(){
                    category_ids.push($(this).val());
                });
                obj[name] = category_ids;
            }else{
                obj[name] = e.prop('checked');
            }
        } else {
            if (name == 'tags[]' && value != '') {
                var tags = JSON.parse(value);
                var tags_parsed = tags.map(item => item.value);
                value = tags_parsed.join(',');
            }
            obj[name] = value;
        }
        var updatedData = JSON.stringify(obj);
        localStorage.setItem('tempdataproduct_'+data_type, updatedData);
        localStorage.setItem('tempload_'+data_type, 'yes');
    }

    /*
        The following code takes the localstorage and sets it in the form. Ensure all types of inputs except files.
    */

    $(document).ready(function(){
        var data_type = $('#data_type').val();
        var tempload = localStorage.getItem('tempload_'+data_type);
        var tags_defined = 'no';
        if(tempload == 'yes'){
            var tempData = localStorage.getItem('tempdataproduct_'+data_type);
            var obj = JSON.parse(tempData);
            for (var key in obj) {
                if (key == 'description' || key == 'meta_description') {
                    $('[name="'+key+'"').html(obj[key]);
                } else {
                    if(($('[name="'+key+'"').attr('type') == 'text') || ($('[name="'+key+'"').attr('type') == 'number')){
                        if (key == 'tags[]'){
                            $('[name="'+key+'"').val(obj[key]);	
                            AIZ.plugins.tagify();
                            tags_defined = 'yes';
                        }else{
                            $('[name="'+key+'"').val(obj[key]);	
                        }			
                    }else if($('[name="'+key+'"').attr('type') == 'checkbox'){
                        if (key == 'category_ids[]') {
                            for (let i = 0; i < obj[key].length; i++) {
                                const element = obj[key][i];
                                $('#treeview input:checkbox#'+element).prop('checked',true);
                                $('#treeview input:checkbox#'+element).parents( "ul" ).css( "display", "block" );
                                $('#treeview input:checkbox#'+element).parents( "li" ).children('.las').removeClass( "la-plus" ).addClass('la-minus');
                            }
                        }else{
                            $('[name="'+key+'"').prop('checked', obj[key]);
                        }			
                    }else if($('[name="'+key+'"').attr('type') == 'radio'){
                        $('#treeview input:radio[value='+obj[key]+']').prop('checked',true);	
                    }else{
                        $('[name="'+key+'"').val(obj[key]).change()
                    }
                }
            }
        }
        if(tags_defined == 'no'){
            AIZ.plugins.tagify();
        }
    });

    /*
        there should be a "Clear Tempdata" button on the add product page. Upon clicking it the following code shall run.
    */
    function clearTempdata() {
        var data_type = $('#data_type').val();
        localStorage.setItem('tempdataproduct_'+data_type, '{}');
		localStorage.setItem('tempload_'+data_type, 'no');
		location.reload();
    }
		
</script>