<?php 
require_once('../../../config.php');
header('Content-type: text/javascript'); ?>

(function($){
/**
 * jqGrid English Translation
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/ 
$.jgrid = {
	defaults : {
		recordtext: '<?php echo get_string('recordtext','block_tts') ?>',
		emptyrecords: '<?php echo get_string('emptyrecords','block_tts') ?>',
		loadtext: '<?php echo get_string('loadtext','block_tts') ?>',
		pgtext : '<?php echo get_string('pgtext','block_tts') ?>'
	},
	search : {
		caption: '<?php echo get_string('search_caption','block_tts') ?>',
		Find: '<?php echo get_string('search_Find','block_tts') ?>',
		Reset: '<?php echo get_string('search_Reset','block_tts') ?>',
		odata : ['<?php echo get_string('eq','block_tts') ?>', '<?php echo get_string('neq','block_tts') ?>', '<?php echo get_string('lt','block_tts') ?>', '<?php echo get_string('le','block_tts') ?>','<?php echo get_string('gt','block_tts') ?>','<?php echo get_string('ge','block_tts') ?>', '<?php echo get_string('bw','block_tts') ?>','<?php echo get_string('nbw','block_tts') ?>','<?php echo get_string('ii','block_tts') ?>','<?php echo get_string('nii','block_tts') ?>','<?php echo get_string('ew','block_tts') ?>','<?php echo get_string('dew','block_tts') ?>','<?php echo get_string('con','block_tts') ?>','<?php echo get_string('dcon','block_tts') ?>'],
		groupOps: [	{ op: "AND", text: "<?php echo get_string('AND','block_tts') ?>" },	{ op: "OR",  text: "<?php echo get_string('OR','block_tts') ?>" }	],
		matchText: " <?php echo get_string('search_matchText','block_tts') ?>",
		rulesText: " <?php echo get_string('search_rulesText','block_tts') ?>"
	},
	edit : {
		addCaption: "<?php echo get_string('add_Caption','block_tts') ?>",
		editCaption: "<?php echo get_string('edit_Caption','block_tts') ?>",
		bSubmit: "<?php echo get_string('edit_bSubmit','block_tts') ?>",
		bCancel: "<?php echo get_string('edit_bCancel','block_tts') ?>",
		bClose: "<?php echo get_string('edit_bClose','block_tts') ?>",
		saveData: "<?php echo get_string('edit_saveData','block_tts') ?>",
		bYes : "<?php echo get_string('edit_bYes','block_tts') ?>",
		bNo : "<?php echo get_string('edit_bNo','block_tts') ?>",
		bExit : "<?php echo get_string('edit_bExit','block_tts') ?>",
		msg: {
			required:"<?php echo get_string('required','block_tts') ?>",
			number:"<?php echo get_string('number','block_tts') ?>",
			minValue:"<?php echo get_string('minValue','block_tts') ?> ",
			maxValue:"<?php echo get_string('maxValue','block_tts') ?>",
			email: "<?php echo get_string('email','block_tts') ?>",
			integer: "<?php echo get_string('integer','block_tts') ?>",
			date: "<?php echo get_string('date','block_tts') ?>",
			url: "<?php echo get_string('url','block_tts') ?>",
			nodefined : " <?php echo get_string('nodefined','block_tts') ?>",
			novalue : " <?php echo get_string('novalue','block_tts') ?>",
			customarray : "<?php echo get_string('customarray','block_tts') ?>",
			customfcheck : "<?php echo get_string('customfcheck','block_tts') ?>"
			
		}
	},
	view : {
		caption: "<?php echo get_string('view_caption','block_tts') ?>",
		bClose: "<?php echo get_string('view_bclose','block_tts') ?>"
	},
	del : {
		caption: "<?php echo get_string('delete_caption','block_tts') ?>",
		msg: "<?php echo get_string('delete_msg','block_tts') ?>",
		bSubmit: "<?php echo get_string('delete_bSubmit','block_tts') ?>",
		bCancel: "<?php echo get_string('delete_bCancel','block_tts') ?>"
	},
	nav : {
		edittext: "<?php echo get_string('edittext','block_tts') ?>",
		edittitle: "<?php echo get_string('edittitle','block_tts') ?>",
		addtext:"<?php echo get_string('addtext','block_tts') ?>",
		addtitle: "<?php echo get_string('addtitle','block_tts') ?>",
		deltext: "<?php echo get_string('deltext','block_tts') ?>",
		deltitle: "<?php echo get_string('deltitle','block_tts') ?>",
		searchtext: "<?php echo get_string('searchtext','block_tts') ?>",
		searchtitle: "<?php echo get_string('searchtitle','block_tts') ?>",
		refreshtext: "<?php echo get_string('refreshtext','block_tts') ?>",
		refreshtitle: "<?php echo get_string('refreshtitle','block_tts') ?>",
		alertcap: "<?php echo get_string('alertcap','block_tts') ?>",
		alerttext: "<?php echo get_string('alerttext','block_tts') ?>",
		viewtext: "<?php echo get_string('viewtext','block_tts') ?>",
		viewtitle: "<?php echo get_string('viewtitle','block_tts') ?>"
	},
	col : {
		caption: "<?php echo get_string('col_caption','block_tts') ?>",
		bSubmit: "<?php echo get_string('col_bSubmit','block_tts') ?>",
		bCancel: "<?php echo get_string('col_bCancel','block_tts') ?>"
	},
	errors : {
		errcap : "<?php echo get_string('errcap','block_tts') ?>",
		nourl : "<?php echo get_string('nourl','block_tts') ?>",
		norecords: "<?php echo get_string('norecords','block_tts') ?>",
		model : "<?php echo get_string('model','block_tts') ?>"
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0.00'},
		currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0.00'},
		date : {
			dayNames:   [
				"<?php echo get_string('Sun','block_tts') ?>", "<?php echo get_string('Mon','block_tts') ?>", "<?php echo get_string('Tue','block_tts') ?>", "<?php echo get_string('Wed','block_tts') ?>", "<?php echo get_string('Thr','block_tts') ?>", "<?php echo get_string('Fri','block_tts') ?>", "<?php echo get_string('Sat','block_tts') ?>",
				"<?php echo get_string('Sunday','block_tts') ?>", "<?php echo get_string('Monday','block_tts') ?>", "<?php echo get_string('Tuesday','block_tts') ?>", "<?php echo get_string('Wednesday','block_tts') ?>", "<?php echo get_string('Thursday','block_tts') ?>", "<?php echo get_string('Friday','block_tts') ?>", "<?php echo get_string('Saturday','block_tts') ?>"
			],
			monthNames: [
				"<?php echo get_string('Jan','block_tts') ?>", "<?php echo get_string('Feb','block_tts') ?>", "<?php echo get_string('Mar','block_tts') ?>", "<?php echo get_string('Apr','block_tts') ?>", "<?php echo get_string('May','block_tts') ?>", "<?php echo get_string('Jun','block_tts') ?>", "<?php echo get_string('Jul','block_tts') ?>", "<?php echo get_string('Aug','block_tts') ?>", "<?php echo get_string('Sep','block_tts') ?>", "<?php echo get_string('Oct','block_tts') ?>", "<?php echo get_string('Nov','block_tts') ?>", "<?php echo get_string('Dec','block_tts') ?>",
				"<?php echo get_string('January','block_tts') ?>", "<?php echo get_string('February','block_tts') ?>", "<?php echo get_string('March','block_tts') ?>", "<?php echo get_string('April','block_tts') ?>", "<?php echo get_string('May','block_tts') ?>", "<?php echo get_string('June','block_tts') ?>", "<?php echo get_string('July','block_tts') ?>", "<?php echo get_string('August','block_tts') ?>", "<?php echo get_string('September','block_tts') ?>", "<?php echo get_string('October','block_tts') ?>", "<?php echo get_string('November','block_tts') ?>", "<?php echo get_string('December','block_tts') ?>"
			],
			AmPm : ["<?php echo get_string('am','block_tts') ?>","<?php echo get_string('pm','block_tts') ?>","<?php echo get_string('AM','block_tts') ?>","<?php echo get_string('PM','block_tts') ?>"],
			S: function (j) {return j < 11 || j > 13 ? ['st', 'nd', 'rd', 'th'][Math.min((j - 1) % 10, 3)] : 'th'},
			srcformat: 'Y-m-d',
			newformat: 'd/m/Y',
			masks : {
				ISO8601Long:"Y-m-d H:i:s",
				ISO8601Short:"Y-m-d",
				ShortDate: "n/j/Y",
				LongDate: "l, F d, Y",
				FullDateTime: "l, F d, Y g:i:s A",
				MonthDay: "F d",
				ShortTime: "g:i A",
				LongTime: "g:i:s A",
				SortableDateTime: "Y-m-d\\TH:i:s",
				UniversalSortableDateTime: "Y-m-d H:i:sO",
				YearMonth: "F, Y"
			},
			reformatAfterEdit : false
		},
		baseLinkUrl: '',
		showAction: '',
		target: '',
		checkbox : {disabled:true},
		idName : 'id'
	},
        
        //This is where main data goes for your grid.js file
        base: {
            Col_1: "<?php echo get_string('col_1','block_tts') ?>",
            Col_2: "<?php echo get_string('col_2','block_tts') ?>",

            
            table_caption: "<?php echo get_string('table_caption','block_tts') ?>",
            err_add: "<?php echo get_string('err_add','block_tts') ?>",
            err_del: "<?php echo get_string('err_del','block_tts') ?>",
             err_save: "<?php echo get_string('err_save','block_tts') ?>",
        
        }
};
})(jQuery);
