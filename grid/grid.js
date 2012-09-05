var LangObject = $.jgrid.base;

jQuery("#lexicon_table").jqGrid({ 
    url:'lexicon_lib.php?courseid='+window.courseid, 
    datatype: "json", 
    colNames:[LangObject.Col_1,LangObject.Col_2], 
    colModel:[ 
    {
        name:'expression',
        label:LangObject.Col_1, 
        index:'expression',
        search:true, 
        stype:'text',
        firstsortorder:'ASC', 
        width:300,
        editable:true,
        sortable:true,
        sorttype:'text',
        edittype:'text',
        editoptions: {
            size:30
        }
    },
{
    name:'prenounce',
    label:LangObject.Col_2, 
    width:300,
    search:false, 
    editable:true,
    sortable:false,
    edittype:'text',
    editoptions: {
        size:30
    }
},], 
rowList:[10,20,30,50,100], 
pager: '#lexicon_pager', 
sortname: 'expression', 
loadonce: true, 
viewrecords: true, 
sortorder: "asc", 
editurl: 'lexicon_lib.php?courseid='+window.courseid,
caption:LangObject.table_caption,
jsonReader : {
    root: "rows",
    page: "page",
    total: "total",
    records: "records",
    repeatitems: false,
    id: "id"
     
},
onSelectRow: function(rowid){ 

}
   
   
   
   
}); 
//("#lexicon_table").jqGrid('navGrid','#lexicon_pager',{});
jQuery("#lexicon_table").navGrid('#lexicon_pager',{view:false}, {

        checkOnSubmit:true,
        afterSubmit : function(response, postdata)
        {

            var success;
            var message;
            var new_id = null;
            if(response.responseText == 1){
                success = true;
                message = ""
            } else {
                success = false;
                message = LangObject.err_save
            }


            return [success,message,new_id]
        },
        closeAfterEdit:true
   
}, {
    //add
    checkOnSubmit:true,
        afterSubmit : function(response, postdata)
        {

            var success;
            var message;
            var new_id = null;
            if(response.responseText <= 0 || isNaN(response.responseText)){
                success = false;
                message = LangObject.err_add;
                response.responseText = 0;
            } else {
                success = true;
                message = ""
                
            }


            return [success,message,response.responseText]
        },
        closeAfterAdd:true
    
    
    
}, {
    //del
    checkOnSubmit:true,
        afterSubmit : function(response, postdata)
        {

            var success;
            var message;
            var new_id = null;
            if(response.responseText == 1){
                success = true;
                message = ""
            } else {
                success = false;
                message = LangObject.err_del;
            }


            return [success,message]
        }
    
}, {
    //search
    sopt:['eq','bw','bn','in','ni','ew','en','cn','nc']
    
}, {
    //view (unused)
    
});

//jQuery("#lexicon_table").jqGrid('searchGrid', {} );
