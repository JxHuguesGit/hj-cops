$(document).ready(function() {
    //////////////////////////////////////////////////////////////////////////////
    // Nouvelle implémentation 27/01/2024
    //////////////////////////////////////////////////////////////////////////////
    $('html').height('100%');
    $('body').height('100%');

    if($('.login-panel').length!=0) {
        $(document).bind('mousemove', function(e) {
            $('.login-panel').addClass('active');
        });
        $(window).bind('keydown', function(e){
            $('.login-panel').addClass('active');
        });
    }

    $('.ajaxAction[data-trigger="click"]').on('click', function(e){
        ajaxActionClick($(this), e);
    });

    $('.ajaxAction[data-trigger="change"]').on('change', function(){
        ajaxActionChange($(this));
    });

    if ($('.mailbox-controls').length!=0) {
        enableMailboxControls();
        // Action sur les checkboxes individuelles
        $('.mailbox-messages input[type=\'checkbox\']').click(function() {
            enableMailboxControls();
        });
    }

    $('.nav-tabs a.nav-link').on('click', function(e) {
        e.preventDefault();
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        $(this).addClass('active');
        $($(this).attr('href')).addClass('show active')
    });

    //////////////////////////////////////////////////////////////////////////////
    // Fin Nouvelle implémentation 27/01/2024
    //////////////////////////////////////////////////////////////////////////////



  // Interface Inbox
  // On s'appuie sur la présence du block "mailbox-controls"
    // Pas d'actions individuelles pour le moment

    // Action sur le reply global
    // Action sur le transfert global
    // Action sur le refresh global
    // Action sur le bouton Précédent
    // Action sur le bouton Suivant

  // Interface Compose
  if ($('.mailbox-read-info').length!=0) {
    $('.card-footer button').on('click', function() {
      $('#mailFrom').attr('disabled', false);
      $('#mailContent').html($('#noteEditable').html());
      $('#writeAction').val($(this).data('action'));
      $('form#writeForm').submit();
    });
    // Saisie dans le destinataire vérifie l'existence et transforme en joli badge en cas de saisie de ; ?
    // Sur un clic Brouillon, le message est enregistré dans les Brouillons. Note : comment gérer l'enregistrement des destinataires lors du Brouillon ?
    // Sur un clic Envoyer, le message est enregistré et envoyé
    // Sur un clic Annuler, on retourne au dossier Réception
    // La saisie du message doit pouvoir être mise en style.
    // Envisager à terme l'ajout de boutons pour styler
  }

  // Interface Compose
  if ($('.enquete-main-info').length!=0) {
	  $('.enquete-main-info a.nav-link').unbind().on('click', function() {
		  let tab = $(this).data('tab');
		  $('.enquete-main-info + div .note-frame').hide();
		  $(tab).show();
		  $('.enquete-main-info a.nav-link').removeClass('bg-primary');
		  $(this).addClass('bg-primary');
	  });
	  $('#writeForm input').on('blur', function() {
		  $(this).removeClass('border-danger');
	  });
	  
	  $('button[type="submit"]').on('click', function() {
		  $('#writeForm input[required]').each(function() {
			  if ($(this).val()=='') {
				  $(this).addClass('border-danger').focus();
				  return false;
			  }
		  }); 
	  });
	  $('.note-editable').on('blur', function(){
        $($(this).data('input')).html($(this).html());
      });
      $('.note-editable').each(function(){
        $($(this).data('input')).html($(this).html());
      });
  }
  
  if ($('textarea[data-resize="auto"]').length!=0) {
    $('textarea[data-resize="auto"]').on('keyup', function(){
      if ($(this).scrollTop()>0) {
        $(this).height($(this).height()+$(this).scrollTop()+10);
      }
    });
    $('textarea[data-resize="auto"]').trigger('keyup');
  }

  if ($('#calendar').length!=0) {
    stretchColspanEvents();
  }
  $('*[data-trigger="click"]').on('click', function() {
    switch ($(this).data('action')) {
      case 'display' :
        $($(this).data('target')).show();
      break;
      case 'toggle' :
        $($(this).data('target')).toggle();
      break;
      case 'submit' :
        if (controlerFormulaire($(this).data('target'))) {
          $($(this).data('target')).submit();
        } else {
          return false;
        }
      break;
    }
  });

  $('fieldset.collapsible i.feather').on('click', function() {
    $(this).parent().parent().toggleClass('collapsed');
    $(this).toggleClass('icon-chevron-right').toggleClass('icon-chevron-down');
  });
  $('fieldset button[data-bs-toggle="dropdown"]').on('click', function() {
    if ($(this).hasClass('show')) {
      $(this).removeClass('show');
      $(this).next().removeClass('show');
    } else {
      $(this).addClass('show');
      $(this).next().addClass('show');
    }
  });
  $('a.dropdown-item').on('click', function() {
    if ($('#reference').val()=='') {
      $('#reference').val($(this).data('abr'));
    } else {
      $('#reference').val($('#reference').val()+', '+$(this).data('abr'));
    }
    $('fieldset button[data-bs-toggle="dropdown"]').removeClass('show').next().removeClass('show');
    $('#reference').focus();
  });

  $('.accordion-button').on('click', function(){
    $(this).toggleClass('collapsed');
  });
	$('.enquete-main-info .nav a.nav-link').on('click', function(){
		$('.enquete-main-info + div .note-editor').hide();
		$('.enquete-main-info .nav a.nav-link').removeClass('bg-primary');
		$(this).addClass('bg-primary');
		$($(this).data('tab')).show();
		$($(this).data('tab')+' .note-editable').focus();
	});
});

function stretchColspanEvents() {
	$('.fc-daygrid-event-harness[data-colspan!="0"]').each(function(){
		let tdWidth = $(this).width();
		let nbDays = $(this).data('colspan');
		$(this).css('right', -1*nbDays*(tdWidth+2));
    });
}





function ajaxActionChange(obj) {
  let id = obj.attr('id');
  let actions = obj.data('ajax').split(',');
  for (let oneAction of actions) {
    switch (oneAction) {
      case 'saveData' :
        saveData(obj);
      break;
      case 'checkLangue' :
        checkLangues();
        checkCaracFormulaire();
      break;
      case 'checkCarac' :
        if (checkCaracteristique(id)) {
          checkCaracteristiques();
          if (id=='carac-carrure') {
            $('#carac-health-points').val(20+3*obj.val());
          } else if (id=='carac-charme' || id=='carac-education') {
            let maxValue = ($('#carac-charme').val()>$('#carac-education').val() ? $('#carac-charme').val() : $('#carac-education').val());
            $('#card-langues select').each(function(idx) {
              if (idx<maxValue) {
                $(this).show();
              } else {
                $(this).hide();
              }
            });
            checkLangues();
          }
          checkCaracFormulaire();
        }
      break;
      default :
        console.log(oneAction+" n'est pas prévu comme valeur d'action Ajax.");
      break;
    }
  }
}

function saveData(obj) {
  let data = {'action': 'dealWithAjax', 'ajaxAction': 'saveData', 'field': obj.attr('id'), 'value': obj.val(), 'id': obj.data('objid')};

  // On a un appel ajax pour rechercher les équivalences au numéro
  $.post(
  	ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  ).done(function(response) {
    obj = JSON.parse(response);
    displayToast(obj.toastContent);
  });

}

function checkLangues() {
  let bln_cardLangue_OK = true;
  $('#card-langues select').each(function(){
    if ($(this).is(':visible') && $(this).val()=='') {
      bln_cardLangue_OK = false;
    }
  });
  if (bln_cardLangue_OK) {
    $('#card-langues').addClass('card-success').removeClass('card-warning');
  } else {
    $('#card-langues').addClass('card-warning').removeClass('card-success');
  }
}

function checkCaracFormulaire() {
  if ($('#card-caracs').hasClass('card-success') && $('#card-langues').hasClass('card-success')) {
    $('#card-submit').addClass('card-success').removeClass('card-danger');
    $('#card-submit p').hide();
    $('button[type="submit"]').removeClass('disabled');
  } else {
    $('#card-submit').addClass('card-danger').removeClass('card-success');
    $('#card-submit p').show();
    $('button[type="submit"]').addClass('disabled');
  }
}

function checkCaracteristiques() {
  let sumCaracPoints = 0;
  $('#card-caracs input').each(function(){
    sumCaracPoints += $(this).val()*1;
  });
  if (sumCaracPoints==21) {
    $('#card-caracs').addClass('card-success').removeClass('card-danger card-warning');
  } else if (sumCaracPoints>21) {
    $('#card-caracs').addClass('card-danger').removeClass('card-success card-warning');
    displayToast('<div class="toast show bg-danger"><div class="toast-header"><i class="fas fa-exclamation-circle mr-2"></i><strong class="me-auto">OOps</strong></div><div class="toast-body">Vous ne disposez que de 21 points à répartir entre vos caractéristiques.</div></div>');
  } else {
    $('#card-caracs').addClass('card-warning').removeClass('card-danger card-success');
  }
    // On doit vérifier que le nombre de points disponibles n'a pas été complétement dépensé.
    // Sinon, ça a l'air d'être bon.
}

function checkCaracteristique(id) {
  let bln_OK = true;
  let value = $('#'+id).val();
  // On va vérifier que la caractéristique est supérieure ou égale à 2 et inférieure ou égale à 5
  if (value<2) {
    displayToast('<div class="toast show bg-warning"><div class="toast-header"><i class="fas fa-exclamation-circle mr-2"></i><strong class="me-auto">OOps</strong></div><div class="toast-body">Une caractéristique ne peut pas être plus basse que 2.</div></div>');
    bln_OK = false;
    $('#card-caracs').addClass('card-danger').removeClass('card-success card-warning');
  } else if (value>5) {
    displayToast('<div class="toast show bg-warning"><div class="toast-header"><i class="fas fa-exclamation-circle mr-2"></i><strong class="me-auto">OOps</strong></div><div class="toast-body">Une caractéristique ne peut pas être plus élevée que 5.</div></div>');
    bln_OK = false;
    $('#card-caracs').addClass('card-danger').removeClass('card-success card-warning');
  } else if (value==5) {
    // On doit vérifier si elle vaut 5 que c'est la seule
    $('#'+id).addClass('maxCarac');
    if ($('.maxCarac').length>1) {
      displayToast('<div class="toast show bg-warning"><div class="toast-header"><i class="fas fa-exclamation-circle mr-2"></i><strong class="me-auto">OOps</strong></div><div class="toast-body">Une seule caractéristique peut être initialisée à 5.</div></div>');
      bln_OK = false;
      $('#card-caracs').addClass('card-danger').removeClass('card-success card-warning');
    }
  }
  return bln_OK;
}

function displayToast(value) {
  $('#toastPlacement').append(value);
  $('#toastPlacement .toast:last-child').delay(5000).hide(0);
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// Gestion des contrôles des formulaires
//////////////////////////////////////////////////////////////////////////////
// Fonction d'entrée pour rediriger vers le bon contrôle
function controlerFormulaire(formId) {
  let blnOk = false;
  if (formId=='#creerNewEvent') {
    blnOk = controlerFormulaireCreerNewEvent();
  }
  return blnOk;
}
// Fonction de contrôler pour création d'un nouvel événement
function controlerFormulaireCreerNewEvent() {
  $('#creerNewEvent .border-danger').removeClass('border-danger');
  let blnOk = true;
  if (estVide('#eventLibelle')) {
    $('#eventLibelle').addClass('border-danger');
    blnOk = false;
    console.log('Le libellé doit être saisi.');
  }
  if (blnOk && estDateSuperieure('#dateDebut', '#dateFin')) {
    $('#dateDebut').addClass('border-danger');
    $('#dateFin').addClass('border-danger');
    blnOk = false;
    console.log('La date de début doit être inférieure à la date de fin.');
  } else if (blnOk && estDateEgale('#dateDebut', '#dateFin') && $('#event_allday:checked')==undefined) { // Seulement égale ici
    if ($('#event_start_hour').val()>$('#event_end_hour').val()) {
      $('#event_start_hour').addClass('border-danger');
      $('#event_end_hour').addClass('border-danger');
      blnOk = false;
    }
    if ($('#event_start_hour').val()==$('#event_end_hour').val() && $('#event_start_minutes').val()>$('#event_end_minutes').val()) {
      $('#event_start_minutes').addClass('border-danger');
      $('#event_end_minutes').addClass('border-danger');
      blnOk = false;
    }
  }
  return blnOk;
}

//////////////////////////////////////////////////////////////////////////////
// Fonctions utilitaires
// L'objet passé en paramètre est-il vide ?
function estVide(target) {
  return ($(target).val().trim()=='');
}
// La valeur de l'objet passé en paramètre est-il une date
function estDateValide(target) {
  let blnOk = true;
  if (estVide(target)) {
    blnOk = false;
  } else {
    let datas = $(target).val().trim().split('/');
    if (datas.length!=3) {
      blnOk = false;
    }
  }
  return blnOk;
}
// La première date est-elle supérieure à la deuxième
function estDateSuperieure(dStart, dEnd) {
  let dataStart = $(dStart).val().trim().split('/');
  let dateStart = new Date(dataStart[2], dataStart[1], dataStart[0]);
  let dataEnd   = $(dEnd).val().trim().split('/');
  let dateEnd   = new Date(dataEnd[2], dataEnd[1], dataEnd[0]);
  return (dateStart>dateEnd);
}
// La première date est-elle égale à la deuxième
function estDateEgale(dStart, dEnd) {
  let dataStart = $(dStart).val().trim().split('/');
  let dataEnd   = $(dEnd).val().trim().split('/');
  console.log(dataStart);
  console.log(dataEnd);
  return (dataStart[2]==dataEnd[2] && dataStart[1]==dataEnd[1] && dataStart[0]==dataEnd[0]);
}
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function csvExport(obj) {
  let data = {'action': 'dealWithAjax', 'ajaxAction': 'csvExport', 'natureId': obj.data('natureid')};

  // On a un appel ajax pour rechercher les équivalences au numéro
  $.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
  }).done(function(response) {
    obj = JSON.parse(response);
    displayToast(obj.toastContent);
    /*
  }).done(function(response) {
	let a = $("<a />", {
               href: "data:text/csv," 
                     + URL.createObjectURL(new Blob([response], {
                         type:"text/csv"
                       })),
               "download":"filename.csv"
            });	
            $("body").append(a);
            a[0].click();
    */
  });
  
}

//////////////////////////////////////////////////////////////////////////////
// Nouvelle implémentation 27/01/2024
//////////////////////////////////////////////////////////////////////////////
function ajaxActionClick(obj, e) {
	let actions = obj.data('ajax').split(',');
	for (let oneAction of actions) {
	    switch (oneAction) {
            case 'openConfirmModal' : {
                let title = obj.data('title');
                let message = obj.data('message');
                let hrefConfirm = obj.data('href');
                openConfirmModal(title, message, hrefConfirm);
            }
            break;
            case 'refresh' :
                window.location.reload();
            break;
            case 'selectAll' :
                selectAll(obj);
            break;
            case 'trash' :
                confirmTrash(obj);
            break;
            /*
			case 'csvExport' :
				csvExport(obj);
				break;
            */
            case 'skillCreation' :
                skillCreation(obj, e);
                break;
		}
	}
}

function confirmTrash(obj) {
    if (!obj.hasClass('disabled')) {
        let title = "Confirmation de la suppression";
        let get = [];
        location.search.replace('?', '').split('&').forEach(function(val) { let split = val.split("=", 2); get[split[0]] = split[1]; });
        let locationHref = location.href;
        let folder = locationHref.substring(locationHref.lastIndexOf('/')+1);
        if (folder=='') {
            locationHref = locationHref.substring(0, locationHref.length-1);
            folder = locationHref.substring(locationHref.lastIndexOf('/')+1);
        }
        console.log(folder);
        let message = "Les messages sélectionnés seront"+(folder=="trash" ? " définitivement" : "")+" supprimés.";
        let ids = $(".mailbox-messages input:checked").map(function(){ return $(this).val(); }).get().join();
        let hrefConfirm = "/"+folder+"/?action=trash&ids="+ids;
        openConfirmModal(title, message, hrefConfirm);
    }
}

function openConfirmModal(title, message, hrefConfirm) {
    $('#modal-confirm').addClass('show').show().unbind().click(function() {
        closeModal('#modal-confirm');
    });
    $('#modal-confirm .modal-title').html(title);
    $('#modal-confirm .modal-body p').html(message);
    $('#modal-confirm .modal-footer a').attr('href', hrefConfirm);
    $('#modal-confirm button[data-dismiss="modal"]').unbind().click(function() {
        closeModal('#modal-confirm');
    });
  
}

function closeModal(id) {
    $(id).removeClass('show').hide();
}
  
  
function selectAll(obj) {
    // On est en train de cliquer sur un bouton de sélection globale.
    let blnChecked = obj.find('i').hasClass('fa-square-check');
    if (blnChecked) {
        // on va décocher tout ce qu'il y a à décocher
        $('.ajaxAction[data-ajax="selectAll"] i').removeClass('fa-square-check').addClass('fa-square');
        $('.mailbox-messages input[type=\'checkbox\']').prop('checked', false);
    } else {
        // on va cocher tout ce qu'il y a à cocher
        $('.ajaxAction[data-ajax="selectAll"] i').removeClass('fa-square').addClass('fa-square-check');
        $('.mailbox-messages input[type=\'checkbox\']').prop('checked', true);
    }
    enableMailboxControls();
}

function enableMailboxControls() {
    let checkeds = $('.mailbox-messages input[type=\'checkbox\']:checked').length;
    if (checkeds>1) {
      $('.mailbox-controls .fa-trash-alt').parent().removeClass('disabled');
      $('.mailbox-controls .fa-reply').parent().addClass('disabled');
      $('.mailbox-controls .fa-share').parent().addClass('disabled');
    } else if (checkeds==1) {
      $('.mailbox-controls .fa-trash-alt').parent().removeClass('disabled');
      $('.mailbox-controls .fa-reply').parent().removeClass('disabled');
      $('.mailbox-controls .fa-share').parent().removeClass('disabled');
    } else {
      $('.mailbox-controls .fa-trash-alt').parent().addClass('disabled');
      $('.mailbox-controls .fa-reply').parent().addClass('disabled');
      $('.mailbox-controls .fa-share').parent().addClass('disabled');
    }
}

function skillCreation(obj, e) {
    let speclevel = obj.data('speclevel');
    let skillid = obj.data('skillid');
    let score = obj.data('score');

    if (score==-1 || score!=speclevel+1) {
        console.log('ajout compétence');
    } else {
        let ul = obj.parent().parent();
        let skilllevel = ul.data('skilllevel');
        $('button[data-btnlevel="'+skilllevel+'"]').html(obj.html()).trigger('click');
        ul.next().hide();
        ul.next().next().show();

        $('ul[data-skilllevel="'+(skilllevel*1+1)+'"] li a').each(function(){
            if ($(this).data('parentid')==skillid) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
        e.preventDefault();
    }
}