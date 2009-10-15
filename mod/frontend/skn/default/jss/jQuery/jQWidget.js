/**
 * Easy Widgets jQuery plugin 1.7
 * 
 * David Esperalta <http://www.davidesperalta.com/>
 * <http://www.bitacora.davidesperalta.com/archives/projects/easywidgets/>
 *
 * Please, use the included documentation and examples for information about
 * how use this plugin. This plugin as been tested in last version of Firefox,
 * Opera, IExplorer, Safari, Chrome and Konqueror.
 *
 * I base my work on a tutorial writen by James Padolsey
 * <http://nettuts.com/tutorials/javascript-ajax/inettuts/>
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Widgets. If not, see <http://www.gnu.org/licenses/>
 * 
 */

(function($){

  /**
   * Main public method of plugin
   * 
   * This method receive the settings argument with some options. If
   * no argument is receive the method use the default plugin settins.
   *
   * See the default settings for this method bellow in this same script.
   *
   * @access public
   * @param settings Array with plugin settings
   * @return Boolean True in every case
   *
   */
  $.fn.EasyWidgets = function(settings){
    
    /**
     * Recursively extend settings with default plugin settings
     * Put the settings in a short variable for we convenience.
     *
     */
    var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
    
    /**
     * By default the Widgets editbox are hidden. Is possible to do
     * this directly from CSS, but, in any case this assert that edit
     * box are hidden.
     *
     */
    $(s.selectors.editbox).hide();
    
    /**
     * Prepare Widget header menu links container
     */
    var widgetMenu = '<span class="' + s.selectors
     .widgetMenu.replace(/\./, '') + '"></span>';

    /**
     * Append Widget menu links to Widgets header
     */
    $(widgetMenu).appendTo(s.selectors.header);

    /**
     * Prepare Widgets IDs array to use later
     */
    var widgetsIds = new Array();

    /**
     * Iterate the Widgets found in the document, in other words
     * execute some actions for every Widgets found in the document.
     *
     */
    $(s.selectors.widget).each(function(widgetCount){
      
      /**
       * Initialize some variables for we convenience
       */
      var cookieValue = '';
      var thisWidget = $(this);
      var thisWidgetId = thisWidget.attr('id');
      var haveWidgetId = $.trim(thisWidgetId) != '';
      var thisWidgetMenu = thisWidget.find(s.selectors.widgetMenu);
      var thisWidgetContent = thisWidget.find(s.selectors.content);

      if(haveWidgetId){
        // Store the Widget ID, if anyone found
        widgetsIds[widgetCount] = thisWidgetId;
      }

      /**
       * Find if the Widget must be closed (hidden)
       *
       * We base this issue in certain cookie. So, if this Widget ID
       * are saved in the "Close Widgets cookie", considerer that this
       * Widgets must be hide from the begin.
       *
       */
      if(haveWidgetId && s.behaviour.useCookies
       && GetCookie(s.cookies.closeName)){
         cookieValue = GetCookie(s.cookies.closeName);
         if(cookieValue.indexOf(thisWidgetId) != -1){
           thisWidget.hide();
         }
      }

      /**
       * Prepare the collapse/extend link for this Widget
       */
      var collapseLink = '';
      
      /**
       * This Widget have a collapse/extend link only if the appropiate
       * option is found in the Widget HTML markup. So, in other case this
       * Widget cannot be collapse, and cannot have the collapse/extend link.
       *
       */
      if(thisWidget.hasClass(s.options.collapsable)){

        /**
         * Take a look: we find if the user want to collapse this Widget
         * from the begin, using another CSS class. If this is the case
         * we hide (collapse) the Widget content right now.
         *
         * In cany case we continue with the collapse/extend link creation,
         * but, the link text and behaviour changes: now can be use to expand
         * the Widget, not to collapse the Widget.
         * 
         */
        if(thisWidget.hasClass(s.options.collapse)){
          collapseLink = MenuLink(
            s.i18n.extendText,
            s.i18n.extendTitle,
            s.selectors.collapseLink
          );
          thisWidgetContent.hide();
        }else{
          collapseLink = MenuLink(
            s.i18n.collapseText, 
            s.i18n.collapseTitle,
            s.selectors.collapseLink
          );
        }
        
        /**
         * Find if we must be use cookies. Note that a Widget can be
         * collapse now, determined from the HTML markup, but the cookies
         * are mandatory: even if this Widget is not collapse based on the
         * HTML markup, can be collapse if this Widget is found in the close
         * Widgets cookie.
         *
         */
        if(haveWidgetId && s.behaviour.useCookies &&
         GetCookie(s.cookies.collapseName)){
          cookieValue = GetCookie(s.cookies.collapseName);
          if(cookieValue.indexOf(thisWidgetId) != -1){
            collapseLink = MenuLink(
              s.i18n.extendText,
              s.i18n.extendTitle,
              s.selectors.collapseLink
            );
            thisWidgetContent.hide();
          }
        }

        /**
         * At this point we are prepare the text, title, CSS class and
         * behaviour of this Widget expand/collapse link. Now we handle
         * certain events of this Widget menu link.
         * 
         */
        $(collapseLink).mousedown(function(e){
          
          e.stopPropagation();

        }).click(function(){
          
          /**
           * Initialize some variables for we convenience
           */
          var thisLink = $(this);
          var canExtend = true;
          var canCollapse = true;
          var thisWidget = thisLink.parents(s.selectors.widget);
          var thisWidgetId = thisWidget.attr('id');
          var haveWidgetId = $.trim(thisWidgetId) != '';
          var thisWidgetContent = thisWidget.find(s.selectors.content);
          var contentVisible = thisWidgetContent.css('display') != 'none';

          // First of all
          thisLink.blur();
          
          if(contentVisible){

            /**
             * If Widget content is visible, user want to collapse the Widget.
             *
             * However, a callback function can be executed, and their result
             * determine if this Widget finally can be collapse or not.
             */
            if($.isFunction(s.callbacks.onCollapseQuery)){
              canCollapse = s.callbacks.onCollapseQuery(thisLink, thisWidget);
            }
            
            if(canCollapse){

              /**
               * Hide the Widget content (collapse the Widget) using the
               * appropiate effect.
               */
              ApplyEffect(
                thisWidgetContent,
                s.effects.widgetCollapse,
                s.effects.effectDuration,
                false
              );

              /**
               * At this point the Widget must be collapse, so, prepare
               * this link text and title to be use for expand the Widget.
               */
              thisLink.html(s.i18n.extendText);
              thisLink.attr('title', s.i18n.extendTitle);

              /**
               * Now looking if we must to use cookies. In this case, because
               * the Widget is right now collapse, we need to store the Widget
               * ID in the appropiate cookie, follow the appropiate structure.
               */
              if(s.behaviour.useCookies && thisWidgetId){
                UpdateCookie(thisWidgetId, s.cookies.collapseName, s);
              }

              /**
               * Finally inform that the Widget as been collapse
               */
              if($.isFunction(s.callbacks.onCollapse)){
                s.callbacks.onCollapse(thisLink, thisWidget);
              }
            }
            
          /**
           * Remember the condition? If the Widget content is not visible
           * the user want to expand the Widget (show the content) and not
           * to collapse. Well, make this right now.
           */
          }else{

            /**
             * By default the Widget can be extended, but, a user defined
             * callback can change this and make the Widget not extendable.
             */
            if($.isFunction(s.callbacks.onExtendQuery)){
              canExtend = s.callbacks.onExtendQuery(thisLink, thisWidget);
            }

            /**
             * If finally the Widget can be extended, make this.
             */
            if(canExtend){

              /**
               * At this point the Widget must be extended, so, prepare this
               * link text and title to be use for collapse the Widget.
               */
              thisLink.html(s.i18n.collapseText);
              thisLink.attr('title', s.i18n.collapseTitle);

              /**
               * Show the Widget content (extend the Widget) using the
               * appropiate effect.
               */
              ApplyEffect(
                thisWidgetContent,
                s.effects.widgetExtend,
                s.effects.effectDuration,
                true
              );

              /**
               * Now, we find if must be use cookies. When the Widget are
               * extend, is needed to find the Widget ID in the appropiate
               * cookie, and remove from then, indicate that this Widget
               * is no more collapse.
               */
              if(haveWidgetId && s.behaviour.useCookies){
                CleanCookie(thisWidgetId, s.cookies.collapseName, s);
              }

              /**
               * Finally inform that the Widget as been extended
               */
              if($.isFunction(s.callbacks.onExtend)){
                s.callbacks.onExtend(thisLink, thisWidget);
              }
            }
          }
          
          // To evit default link behaviour
          return false;
          
        }).appendTo(thisWidgetMenu);
        
      } // End of if Widget as collapsable condition
      
      /**
       * Prepare the edit/cancel edit link for this Widget
       */
      var editLink = '';

      /**
       * This Widget have a edit/cancel edit link only if the appropiate
       * option is found in the Widget HTML markup. So, in other case this
       * Widget cannot be edited, and cannot have the edit/edit cancel link.
       *
       */
      if(thisWidget.hasClass(s.options.editable)){
        
        /**
         * Text, title and class (behaviour) for this link
         */
        editLink = MenuLink(
          s.i18n.editText, 
          s.i18n.editTitle,
          s.selectors.editLink
        );

        /**
         * Take a look. Another plugin options can be use to place certain
         * element (a link, a button) with a specific class that can be use
         * to close this Widget editbox.
         *
         * If this Widget contain this element, we bind to this the "click"
         * event and handle this to be use to close this Widget editbox.
         */
        thisWidget.find(s.selectors.closeEdit).click(function(e){

          // For we convenience
          var thisLink = $(this);
          var thisWidget = thisLink.parents(s.selectors.widget);
          var thisEditLink = thisWidget.find(s.selectors.editLink);
          var thisEditbox = thisWidget.find(s.selectors.editbox);

          // First of all
          thisLink.blur();

          /**
           * Hide the Widget editbox (close the editbox) using the
           * appropiate effect.
           */
          ApplyEffect(
            thisEditbox,
            s.effects.widgetCloseEdit,
            s.effects.effectDuration,
            false
          );
          /**
           * Note that we change here the text and title of the Widget edit
           * link placed into the Widget menu container. In other words, the
           * user click the element contained into the editbox, but we change
           * the text and title of the mentioned link, because this task is
           * like the user click this link.
           *
           * So, the link placed into the Widget menu container need right now
           * to change the behaviour: not to use for close the editbox, but
           * use for show the editbox, because, as you can see bellow, the
           * editbox are now hidden or closed.
           */
          thisEditLink.html(s.i18n.editText);
          thisEditLink.attr('title', s.i18n.editTitle);
          
          // To evit default link behaviour
          return false;
        });

        /**
         * At this point we are prepare the text, title, CSS class and
         * behaviour of this Widget edit/cancel edit link. Now we handle
         * certain events of this Widget menu link.
         *
         */
        $(editLink).mousedown(function(e){

          e.stopPropagation();
          
        }).click(function(){

          /**
           * Initialize some variables for we convenience
           */
          var thisLink = $(this);
          var canShow = canHide = true;
          var thisWidget = thisLink.parents(s.selectors.widget);
          var thisEditbox = thisWidget.find(s.selectors.editbox);
          var thisEditboxVisible = thisEditbox.css('display') != 'none';

          // First of all
          thisLink.blur();
          
          if(thisEditboxVisible){

            /**
             * If Widget editbox is visible, user want to cancel (hide) this.
             *
             * However, a callback function can be executed, and their result
             * determine if this Widget editbox finally can be hide or not.
             */
            if($.isFunction(s.callbacks.onCancelEditQuery)){
              canHide = s.callbacks.onCancelEditQuery(thisLink, thisWidget);
            }

            /**
             * If finally the Widget editbox can be hide, make this.
             */
            if(canHide){

              /**
               * Hide the Widget editbox (cancel edit) using the
               * appropiate effect.
               */
              ApplyEffect(
                thisEditbox,
                s.effects.widgetCancelEdit,
                s.effects.effectDuration,
                false
              );

              /**
               * At this point the Widget editbox must be hidden, so, prepare
               * this link text and title to be use for open again the editbox.
               */
              thisLink.html(s.i18n.editText);
              thisLink.attr('title', s.i18n.editTitle);

              /**
               * Finally inform the Widget editbox as been cancel or hidden.
               */
              if($.isFunction(s.callbacks.onCancelEdit)){
                s.callbacks.onCancelEdit(thisLink, thisWidget);
              }
            }
            
          /**
           * Remember the condition? If the Widget editbox is not visible
           * the user want to open or show the Widget editbox. So, make this.
           */
          }else{

            /**
             * By default the Widget editbox can be show, but, a user defined
             * callback can change this and make the Widget editbox not visible.
             */
            if($.isFunction(s.callbacks.onEditQuery)){
              canShow = s.callbacks.onEditQuery(thisLink, thisWidget);
            }

            /**
             * If finally the Widget editbox can be show, make this.
             */
            if(canShow){

              /**
               * At this point the Widget editbox must be show, so, prepare
               * this link text and title to be use for cancel or hidden this
               * Widget editbox.
               */
              thisLink.html(s.i18n.cancelEditText);
              thisLink.attr('title', s.i18n.cancelEditTitle);

              /**
               * Show the Widget editbox (open editbox) using the
               * appropiate effect.
               */
              ApplyEffect(
                thisEditbox,
                s.effects.widgetOpenEdit,
                s.effects.effectDuration,
                true
              );

              /**
               * Finally inform that the Widget editbox as been open.
               */
              if($.isFunction(s.callbacks.onEdit)){
                s.callbacks.onEdit(thisLink, thisWidget);
              }
            }
          }
          
          // To evit default link behaviour
          return false;

        }).appendTo(thisWidgetMenu);

      } // End of if Widget as editable condition

      /**
       * Prepare the remove (close, hide) link of this Widget.
       */
      var removeLink = '';

      /**
       * This Widget have a remove (close, hide) link only if the appropiate
       * option is found in the Widget HTML markup. So, in other case this
       * Widget cannot be removed, and cannot have the remove link.
       *
       */
      if(thisWidget.hasClass(s.options.removable)){

        /**
         * Text, title and class (behaviour) for this link
         */
        removeLink = MenuLink(
          s.i18n.closeText, 
          s.i18n.closeTitle,
          s.selectors.closeLink
        );

        /**
         * At this point we are prepare the text, title, CSS class and
         * behaviour of this Widget remove (close, hide) link. Now we handle
         * certain events of this Widget menu link.
         *
         */
        $(removeLink).mousedown(function(e){

          e.stopPropagation();

        }).click(function(){

          /**
           * Initialize some variables for we convenience
           */
          var canRemove = true;
          var thisLink = $(this);
          var thisWidget = thisLink.parents(s.selectors.widget);
          var thisWidgetId = thisWidget.attr('id');
          var haveWidgetId = ($.trim(thisWidgetId) != '');

          // First of all
          thisLink.blur();

          /**
           * By default the Widget can be remove, but a user defined callback
           * can determine if finally the Widget can be remove or not.
           */
          if($.isFunction(s.callbacks.onCloseQuery)){
            canRemove = s.callbacks.onCloseQuery(thisLink, thisWidget);
          }

          /**
           * If finally the Widget can be remove, make this.
           */
          if(canRemove){
            
            /**
             * Another plugin options can be determine that a confirm dialog
             * must be show to ask the user if really want to remove (close,
             * hide) the Widget.
             *
             * If this option is found, we show the confirm dialog and use the
             * user response in this condition: bassically, if the user confirm
             * the dialog, we remove the Widget.
             *
             * However, note that this dialog is not found if the Widget HTML
             * markup not contain the appropiate class or plugin option.
             * 
             */
            if(!thisWidget.hasClass(s.options.closeConfirm)

             || confirm(s.i18n.confirmMsg)){

               /**
                * At this point the Widget must be remove, so looking if
                * we must be use cookies, to store this Widget ID in the
                * appropiate "Closed Widgets" cookie.
                */
               if(haveWidgetId && s.behaviour.useCookies){
                 UpdateCookie(thisWidgetId, s.cookies.closeName, s);
               }

               /**
                * Remove (close, hide) the Widget using the
                * appropiate effect.
                */
               ApplyEffect(
                 thisWidget,
                 s.effects.widgetClose,
                 s.effects.effectDuration,
                 false
               );

               /**
                * Finally inform that the Widget as been removed.
                */
               if($.isFunction(s.callbacks.onClose)){
                 s.callbacks.onClose(thisLink, thisWidget);
               }
            }
          }
          
          // To evit default link behaviour
          return false;

        }).appendTo(thisWidgetMenu);
      }

    }); // End of document Widget found iteration

    /**
     * At this point the found Widgets having the appropiate menu links.
     *
     * We continue now with other task, like repositioned the Widgets
     * in the document, make the columns and Widgets sortables, etc.
     * 
     */

    /**
     * Now prepare the Widgets repositioned, initialize some variables.
     */
    var i, j = 0;
    var widgetsPositions = ''; 
    
    /**
     * The plugin user can define a callback function to use right here.
     * This function must be return a string that contain the position
     * of the Widgets.
     *
     * If is the case, we use the user provided Widgets positions. But
     * what string can be passed here? The stringn that another callback
     * of this plugin can be send": onChangePositions", as you can see
     * bellow.
     *
     * In other words, the user of the plugin can be use their own system
     * to store the Widgets positions, instead of cookies. For example,
     * the user can store the positions in a database.
     *
     * So, the user receive a string with the Widgets positions when the
     * "onChangePositions" callback is executed (see bellow) and this string
     * is that we expect as the result for the "onRefreshPositions" callback.
     *
     */

    // So, we must to use the user stored Widgets positions?
    if($.isFunction(s.callbacks.onRefreshPositions)){

      // Ok, executed the appropiate callback and save the result
      widgetsPositions = s.callbacks.onRefreshPositions();

      // In other case, we need to obtain the positions from cookies?
    }else if(s.behaviour.useCookies
     && GetCookie(s.cookies.positionName)){

      // Ok, get the appropiate cookie value and save it
      widgetsPositions = GetCookie(s.cookies.positionName)
    }

    /**
     * Use the widget positions string, parse it if not empty. Note
     * that we parse the string follow the structure that we use when
     * the Widgets change the positions.
     *
     * You can see how we prepare this structure in the "stop" callback
     * property of the "sortable items", see bellow in this same script.
     *
     * The string that we refered here and must be parsed can be like
     * bellow string (but without the line break)
     *
     * widget-column-1=identifierwidget-4,identifierwidget-3|widget-column-2=
     * identifierwidget-2,identifierwidget-1
     *
     * So, we have columns (and their Widgets) separed by the | character, and
     * every column part of string contain the Widgets IDs separed by commas.
     *
     */
    if($.trim(widgetsPositions) != ''){

      // Initialize variables
      i = j = 0;

      // Get the columns part, contain column IDs and Widgets
      var columns = widgetsPositions.split('|');

      // Count the total of columns
      var totalColumns = columns.length;

      // For every column that we found
      for(i = 0; i < totalColumns; i++){

        // We can look for this column ID and their Widgets
        var column = columns[i].split('=');

        // The first element in column array contain the column ID
        var columnSel = '#'+column[0];

        // If we can found this column in the document
        if($(columnSel)){

          // Get this column widgets IDs (comma separated)
          var widgets = column[1].split(',');

          // Count the total of Widgets in this column
          var totalWidgets = widgets.length;

          // And try to repositioned every column Widget
          for(j = 0; j < totalWidgets; j++){

            if($.trim(widgets[j]) != ''){

              // Get this Widget ID
              var widgetSel = '#'+widgets[j];

              /**
               * And append the Widget into the appropiate column
               *
               * Note the order of position: is the correct, becuase
               * when we save the positions in the string we use the
               * order that Widgets are found in the column.
               * 
               */
              $(widgetSel).appendTo(columnSel);
            }
          }
        }        
      }
    } // End of Widgets repositioned

   /**
    * Now prepare the Widgets and columns to be sortables elements
    */
    var sortableItems = null;
    
    /**
     * Find first Widgets that must be convert in sortable items
     */
    sortableItems = (function(){

      // Not every must be sortable: fixed Widgets cannot
      var fixedWidgets = '';
      
      /**
       * Iterate for all Widgets in the document
       */
      $(s.selectors.widget).each(function(count){

        /**
         * And find the movable plugin option for every Widget.
         * If this option (CSS class) is not found, the Widget
         * cannot be movable, so, dont make as sortable element.
         * 
         */
        if(!$(this).hasClass(s.options.movable)){

          // We need a ID in any case, if not found one, make one
          if(!this.id){
            this.id = 'fixed-widget-id-' + count;
          }

          // Prepare the appropiate string (selector) to use later
          if(fixedWidgets == ''){
            fixedWidgets += '#'+this.id;
          }else{
            fixedWidgets += ',' + '#'+this.id;
          }
          
        }
      });
      
      /**
       * We know at this point what Widgets are movables (and must be
       * sortable elements) and what not. So, find in the document the
       * Widgets and columns that finally must be make sortable elements.
       *
       * We can prepare now the appropiate selector based in this info.
       * 
       */
      var notSelector = '';

      /**
       * Change for jQuery 1.3 version: If the selector finally end by ":not()"
       * (note the empty parentesis) we have a problem, so, prepare the "not"
       * selector only when is needed (when any fixed widget found).
       */
      if($.trim(fixedWidgets) == ''){

        notSelector = '> '+s.selectors.container;

      }else{

        notSelector = '> '+s.selectors.container+':not(' + fixedWidgets + ')';
      }

      /**
       * Return movable Widgets and every columns to make sortables
       */
      return $(notSelector, s.selectors.columns);
    })();

    /**
     * At this point we can use the sortable items found, and we prepare
     * now the header of sortable Widgets. First of all, set the appropiate
     * cursor for Widget headers, and handle the needed events of this.
     *
     * Take a look at this code. Here we prepare the header of sortable
     * Widgets for the expected behaviour. But here you can see that we
     * looking for certain "dragging" class, for example.
     *
     * This is in relation with jQuery sortable feature, and you can see
     * bellow, when we call the "sortable" function around the columns.
     * Please, continue reading bellow in this same script.
     *
     */
    sortableItems.find(s.selectors.header).css({

      cursor: 'move'

    }).mousedown(function(e){

      var thisHeader = $(this);

      sortableItems.css({width:''});

      // The parent of the header is a Widget
      thisHeader.parent().css({
        width: thisHeader.parent().width() + 'px'
      });

    }).mouseup(function(){

      var thisHeader = $(this);

      if(!thisHeader.parent().hasClass('dragging')){
        thisHeader.parent().css({width:''});
      }else{
        $(s.selectors.columns).sortable('disable');
      }
      
    });

    /**
     * Prepare the sortable behaviour of the Widget columns found
     * in the document. For more information we refer you to the
     * jQuery documentation at: <http://docs.jquery.com/UI/Sortables>
     * 
     */
    $(s.selectors.columns).sortable({
      // Sortable items found and prepared above
      items: sortableItems,
      containment: 'document',
      forcePlaceholderSize: true,
      // Properties by plugin settings
      handle: s.selectors.header,
      delay: s.behaviour.dragDelay,
      revert: s.behaviour.dragRevert,
      opacity: s.behaviour.dragOpacity,
      connectWith: $(s.selectors.columns),
      placeholder: s.selectors.placeHolder,

      start : function(e, ui){
        $(ui.helper).addClass('dragging');
      },

      stop : function(e, ui){
        $(ui.item).removeClass('dragging');

        $(ui.item).css({width : ''});
        $(s.selectors.columns).sortable('enable');

        /**
         * Initialize some variables for we convenience
         */
        var widgetsPosition = '';
        
        /**
         * Retrieve the Widget positions to use later. Remember that
         * we conform here a string that contain a representation of
         * columns and their Widgets positions.
         *
         * This string is use when we repositioned the Widgets (see
         * above in this same script), so, the change that make here
         * must be reflect also when we use the appropiate string.
         * 
         * The string that we refered here and must be parsed can be like
         * bellow string (but without the line break)
         *
         * widget-column-1=identifierwidget-4,identifierwidget-3|
         * widget-column-2=identifierwidget-2,identifierwidget-1
         *
         */
        $(s.selectors.columns).each(function(i){
          
          var thisColumn = this;
          var widgetsValue = '';

          // First part of this column positions string
          var columnValue = thisColumn.id + '=';

          /**
           * Find for this column Widgets
           */
          $(thisColumn).children(s.selectors.widget).each(function(j){

            var thisWidget = this;
            
            // Second part of this column positions string
            if(widgetsValue == ''){
              widgetsValue += thisWidget.id;
            }else{
              widgetsValue += ','+thisWidget.id;
            }
          });

          // Join the string parts
          columnValue += widgetsValue;

          // And save in the Widgets positions string
          if(widgetsPosition == ''){
            widgetsPosition += columnValue;
          }else{
            widgetsPosition += '|' + columnValue;
          }
        });
        
        /**
         * Right now we have a string that contain the columns and their
         * Widgets positions. At this point we must to save this string in
         * the appropiate (positions) cookie.
         *
         * However, the plugin user can determine if we must to use cookies
         * or not, and have the opportunity to save the positions string in
         * a database, for example.
         *
         * So, execute the appropiate plugin callback if is defined, and 
         * send to this the Widgets positions string, that the user can be
         * use when the plugin execute the callback: "onRefreshPositions".
         *
         */
        if($.isFunction(s.callbacks.onChangePositions)){
          s.callbacks.onChangePositions(widgetsPosition);

        // Not callback, maybe we must use cookies
        }else if(s.behaviour.useCookies){

          // And save the positions string
          if(GetCookie(s.cookies.positionName) != widgetsPosition){
            SetCookie(s.cookies.positionName, widgetsPosition, s);
          }
        }

        /**
         * Finally inform that the Widget is now stopped
         */
        if($.isFunction(s.callbacks.onDragStop)){
          s.callbacks.onDragStop(e, ui);
        }
        
        return true;
      }
    });

    /**
     * Find if we must use cookies and the disable cookie is True:
     * in this case simply disable the Widgets.
     */
    if(s.behaviour.useCookies && (GetCookie(s.cookies.disableName) == 1)){
      $.fn.DisableEasyWidgets(s);
    }

    /**
     * At this point the Widgets found in document are prepared to be use.
     *
     * We implement now here the "cleaning cookies" feature. What is this?
     *
     * Imagine that a Widget is closed in some momment, we save the ID in
     * the appropiate cookie, but, this Widget HTML markup is no more print
     * out. Not have sense that this Widget ID continue in the cookie.
     *
     * So, we looking here for Widgets IDs that not exists in the HTML markup
     * but still presents in the "collapsed" and "closed" cookies. Another
     * related cookie, "positions" cookie, is clean automatically, becuase
     * their value is regenerated when a Widget is moved.
     *
     * So, clean the "collapsed" and "closed" cookies from Widgets IDs that
     * is not more printed (used). But take a look at this: we not clean the
     * cookies every time, every user request.
     *
     * We randomize this task, and execute only in certain user request, not
     * in everyone. This save resources, and finally is a good idea to remove
     * the unused Widgets IDs from cookies, but this is not a problem for the
     * correct function of the plugin.
     *
     * Of course, this task is only executed when the "use cookies" option of
     * the plugin is true, when we have some Widgets IDs.
     *
     */
    
    var cleanCookies = (Math.ceil(Math.random() * 3) == 1) 
     && s.behaviour.useCookies && (widgetsIds.length > 0);

    // Clean cookies?
    if(cleanCookies){

      // For we convenience
      i = j = 0;

      // Store the cookies names into an Array to iterate with this
      var cookies = new Array(
        s.cookies.closeName,
        s.cookies.collapseName
      );
        
      var cookiesLen = cookies.length;

      /**
       * For every cookie ("collapsed" and "closed"). Remember that we can
       * iterate for this cookies, becuase the Widgets IDs are stored in the
       * same way: separated by commas.
       *
       */
      for(i = 0; i < cookiesLen; i++){
        
        if(GetCookie(cookies[i])){

          // For we convenience
          var widgetId = '';
          var cleanValue = '';

          // Get the Widgets IDs in this cookies
          var storedValue = GetCookie(cookies[i]).split(',');

          // Count the stored Widgets IDs
          var storedWidgets = storedValue.length;

          // Iterate around the Widgets stored in the cookie
          for(j = 0; j < storedWidgets; j++){

            // Get a Widget ID
            widgetId = $.trim(storedValue[j]);

            // If ID present in the widgets that we found in document?
            if($.inArray(widgetId, widgetsIds) != -1){
              
              // So, this Widget ID can be still in the new cookie value
              if($.trim(cleanValue) == ''){
                 // Alone
                 cleanValue += widgetId;
              }else{
                 // Or with others
                 cleanValue += ','+widgetId;
              }
            }
          }
          
          // At this point we save a cleaning cookie value
          SetCookie(cookies[i], cleanValue, s);
        }
      }
    }
    
  };
  // Yeah! End of the main plugin public method

  /**
   * Another public methods of this plugin
   */

  /**
   * Disable the Widgets
   *
   * This method can be use to disable the Widgets sortable feature.
   *
   * @access public
   * @param settings Array with the plugin options
   * @return Boolean True if Widgets can be disable, False if not
   * 
   */
  $.fn.DisableEasyWidgets = function(settings){
    var canDisable = true;
    var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
    if($.isFunction(s.callbacks.onDisableQuery)){
      canDisable = s.callbacks.onDisableQuery();
    }
    if(canDisable){
      $(s.selectors.columns).sortable('disable');
      $(s.selectors.widget+':visible').each(function(){
        var thisWidget = $(this);
        if(thisWidget.hasClass(s.options.movable)){
          // Because if not is movable this cursor not have sense
          thisWidget.find(s.selectors.header).css('cursor', 'default');
        }
      });
      if($.isFunction(s.callbacks.onDisable)){
        s.callbacks.onDisable();
      }
      SetCookie(s.cookies.disableName, 1, s);
      return true;
    }else{
      return false;
    }
  };

  /**
   * Enable previously disable Widgets
   *
   * This method can be use to re-enable the Widgets sortable feature.
   *
   * @access public
   * @param settings Array with the plugin options
   * @return Boolean True if Widgets can be enable, False if not
   *
   */
  $.fn.EnableEasyWidgets = function(settings){
    var canEnable = true;
    var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
    if($.isFunction(s.callbacks.onEnableQuery)){
      canEnable = s.callbacks.onEnableQuery();
    }
    if(canEnable){
      $(s.selectors.columns).sortable('enable');
      $(s.selectors.widget+':visible').each(function(){
        var thisWidget = $(this);
        if(thisWidget.hasClass(s.options.movable)){
          // Because if not is movable this cursor not have sense
          thisWidget.find(s.selectors.header).css('cursor', 'move');
        }
      });
      if($.isFunction(s.callbacks.onEnable)){
        s.callbacks.onEnable();
      }
      if(s.behaviour.useCookies){
        SetCookie(s.cookies.disableName, 0, s);
      }
      return true;
    }else{
      return false;
    }
  };

  /**
   * Hide all Widgets
   *
   * This method can be use to hide all Widgets.
   *
   * @access public
   * @param settings Plugin settings to be use
   * @return Boolean True in every case
   *
   */
  $.fn.HideEasyWidgets = function(settings){
    var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
    $(s.selectors.widget+':visible').each(function(){
      var canHide = true;
      var thisWidget = $(this);
      var thisWidgetId = thisWidget.attr('id');
      if($.isFunction(s.callbacks.onHideQuery)){
        canHide = s.callbacks.onHideQuery(thisWidget);
      }
      if(canHide){
        ApplyEffect(
          thisWidget,
          s.effects.widgetHide,
          s.effects.effectDuration,
          false
        );
        if(s.behaviour.useCookies && thisWidgetId){
          UpdateCookie(thisWidgetId, s.cookies.closeName, s);
        }
        if($.isFunction(s.callbacks.onHide)){
          s.callbacks.onHide(thisWidget);
        }
      }
    });
    return true;
  };

  /**
   * Show all Widget
   *
   * This method can be use to show all Widgets.
   *
   * @access public
   * @param settings Plugin settings to be use
   * @return Boolean True in every case
   *
   */
  $.fn.ShowEasyWidgets = function(settings){
    var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
    $(s.selectors.widget+':hidden').each(function(){
      var canShow = true;
      var thisWidget = $(this);
      var thisWidgetId = thisWidget.attr('id');
      if($.isFunction(s.callbacks.onShowQuery)){
        canShow = s.callbacks.onShowQuery(thisWidget);
      }
      if(canShow){
        ApplyEffect(
          thisWidget,
          s.effects.widgetShow,
          s.effects.effectDuration,
          true
        );
        if(s.behaviour.useCookies && thisWidgetId){
          CleanCookie(thisWidgetId, s.cookies.closeName, s);
        }
        if($.isFunction(s.callbacks.onShow)){
          s.callbacks.onShow(thisWidget);
        }
      }
    });
    return true;
  };

  /**
   * Show a specific Widget
   *
   * This method can be use to show an individual widget.
   *
   * @access public
   * @param id String Widget identifier to be show
   * @param settings Plugin settings to be use
   * @return Boolean True if Widget can be show, False if not
   *
   */
  $.fn.ShowEasyWidget = function(id, settings){
    var canShow = true;
    var widgetId = '#'+id;
    var thisWidget = $(widgetId);
    if(thisWidget.css('display') == 'none'){
      var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
      if($.isFunction(s.callbacks.onShowQuery)){
        canShow = s.callbacks.onShowQuery(thisWidget);
      }
      if(canShow){
        ApplyEffect(
          thisWidget,
          s.effects.widgetShow,
          s.effects.effectDuration,
          true
        );
        if(s.behaviour.useCookies){
          CleanCookie(id, s.cookies.closeName, s);
        }
        if($.isFunction(s.callbacks.onShow)){
          s.callbacks.onShow(thisWidget);
        }
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  };

  /**
   * Hide a specific Widget
   * 
   * This method can be use to hide an individual widget.
   *
   * @access public
   * @param id String Widget identifier to be hide
   * @param settings Plugin settings to be use
   * @return Boolean True if Widget can be hide, False if not
   * 
   */
  $.fn.HideEasyWidget = function(id, settings){
    var canHide = true;
    var widgetId = '#'+id;
    var thisWidget = $(widgetId);
    if(thisWidget.css('display') != 'none'){
      var s = $.extend(true, $.fn.EasyWidgets.defaults, settings);
      if($.isFunction(s.callbacks.onHideQuery)){
        canHide = s.callbacks.onHideQuery(thisWidget);
      }
      if(canHide){
        ApplyEffect(
          thisWidget,
          s.effects.widgetHide,
          s.effects.effectDuration,
          false
        );
        if(s.behaviour.useCookies){
          // Put this Widget ID in the "closed" cookie
          UpdateCookie(id, s.cookies.closeName, s);
        }
        if($.isFunction(s.callbacks.onHide)){
          s.callbacks.onHide(thisWidget);
        }
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  };
  
  /**
   * Default plugin settings
   *
   * Here we initialize the plugin default settings, that can be overwrite
   * when the user call to plugin public methods. In every case we merge this
   * settings with the user provided, to obtain in any case correct values.
   *
   */
  $.fn.EasyWidgets.defaults = {

    // Behaviour of the plugin
    behaviour : {

      // Miliseconds delay between mousedown and drag start
      dragDelay : 100,

      // Miliseconds delay between mouseup and drag stop
      dragRevert : 100,

      // Determinme the opacity of Widget when start drag
      dragOpacity : 0.8,

      // Cookies (require Cookie plugin) to store positions and states
      useCookies : false
    },

    // Some effects that can be apply sometimes
    effects : {

      // Miliseconds for effects duration
      effectDuration : 500,

      // Can be none, slide or fade
      widgetShow : 'none',
      widgetHide : 'none',
      widgetClose : 'none',
      widgetExtend : 'none',
      widgetCollapse : 'none',
      widgetOpenEdit : 'none',
      widgetCloseEdit : 'none',
      widgetCancelEdit : 'none'
    },

    // Only for the optional cookie feature
    cookies : {

      // Cookie path
      path : '',

      // Cookie domain
      domain : '',

      // Cookie expiration time in days
      expires : 90,

      // Store a secure cookie?
      secure : false,

      // Cookie name for close Widgets
      closeName : 'ew-close',

      // Cookie name for disable all Widgets
      disableName : 'ew-disable',

      // Cookie name for positined Widgets
      positionName : 'ew-position',

      // Cookie name for collapsed Widgets
      collapseName : 'ew-collapse'
    },

    // Options name to use in the HTML markup
    options : {

      // To recognize a movable Widget
      movable : 'movable',

      // To recognize a editable Widget
      editable : 'editable',

      // To recognize a collapse Widget
      collapse : 'collapse',

      // To recognize a removable Widget
      removable : 'removable',

      // To recognize a collapsable Widget
      collapsable : 'collapsable',

      // To recognize Widget that require confirmation when remove
      closeConfirm : 'closeconfirm'
    },

    // Callbacks functions
    callbacks : {

      // When a editbox is closed, send the link and the widget objects
      onEdit : null,

      // When a Widget is show, send the widget object
      onShow : null,

      // When a Widget is hide, send the widget object
      onHide : null,
      
      // When a Widget is closed, send the link and the widget objects
      onClose : null,

      // When Widgets are enabled using the appropiate public method
      onEnable : null,

      // When a Widget is extend, send the link and the widget objects
      onExtend : null,

      // When Widgets are disabled using the appropiate public method
      onDisable : null,

      // When a editbox is closed, send a ui object, see jQuery::sortable()
      onDragStop : null,

      // When a Widget is collapse, send the link and the widget objects
      onCollapse : null,

      // When a editbox is try to close, send the link and the widget objects
      onEditQuery : null,

      // When a Widget is try to show, send the widget object
      onShowQuery : null,

      // When a Widget is try to hide, send the widget object
      onHideQuery : null,

      // When a Widget is try to close, send the link and the widget objects
      onCloseQuery : null,

      // When a editbox is cancel (close), send the link and the widget objects
      onCancelEdit : null,

      // When Widgets are enabled using the appropiate public method
      onEnableQuery : null,

      // When a Widget is try to expand, send the link and the widget objects
      onExtendQuery : null,

      // When Widgets are disabled using the appropiate public method
      onDisableQuery : null,

      // When a Widget is try to expand, send the link and the widget objects
      onCollapseQuery : null,

      // When a editbox is try to cancel, send the link and the widget objects
      onCancelEditQuery : null,

      // When one Widget is repositioned, send the positions serialization
      onChangePositions : null,

      // When Widgets need repositioned, get the serialization positions
      onRefreshPositions : null
    },

    // Selectors in HTML markup. All can be change by you, but not all is
    // used in the HTML markup. For example, the "editLink" or "closeLink"
    // is prepared by the plugin for every Widget.
    selectors : {

      // Container of a Widget (into another element that use as column)
      // The container can be "div" or "li", for example. In the first case
      // use another "div" as column, and a "ul" in the case of "li".
      container : 'div',

      // Class identifier for a Widget
      widget : '.widget',

      // Class identifier for a Widget header (handle)
      header : '.widget-header',

      // Class for the Widget header menu
      widgetMenu : '.widget-menu',

      // Class identifier for a Widget column (parents of Widgets)
      columns : '.widget-column',

      // Class identifier for Widget editboxes
      editbox : '.widget-editbox',

      // Class identifier for Widget content
      content : '.widget-content',

      // Class identifier for editbox close link or button, for example
      closeEdit : '.widget-close-editbox',

      // Class identifier for a Widget edit link
      editLink : '.widget-editlink',

      // Class identifier for a Widget close link
      closeLink : '.widget-closelink',

      // Class identifier for Widgets placehoders
      placeHolder : 'widget-placeholder',

      // Class identifier for a Widget collapse link
      collapseLink : '.widget-collapselink'
    },

    // To be translate the plugin into another languages
    // But this variables can be used to show images instead
    // links text, if you preffer. In this case set the HTML
    // of the IMG elements.
    i18n : {

      // Widget edit link text
      editText : 'Edit',

      // Widget close link text
      closeText : 'Close',

      // Widget extend link text
      extendText : 'Extend',

      // Widget collapse link text
      collapseText : 'Collapse',

      // Widget cancel edit link text
      cancelEditText : 'Cancel',

      // Widget edition link title
      editTitle : 'Edit this widget',

      // Widget close link title
      closeTitle : 'Close this widget',

      // Widget confirmation dialog message
      confirmMsg : 'Remove this widget?',

      // Widget cancel edit link title
      cancelEditTitle : 'Cancel edition',

      // Widget extend link title
      extendTitle : 'Extend this widget',

      // Widget collapse link title
      collapseTitle : 'Collapse this widget'
    }
  };

  /**
   * Some auxiliars private members of the plugin
   */

  /**
   * Get a specific cookie value
   *
   * This function is based in jQuery Cookie plugin by Klaus Hartl
   *
   * @access private
   * @param name String with the cookie name
   * @return Null|String Cookie value or nothing
   *
   */
  function GetCookie(name){
    var result = null;
    if(document.cookie && $.trim(document.cookie) != ''){
      var cookies = document.cookie.split(';');
      var cookiesLen = cookies.length;
      if(cookiesLen > 0){
        for(var i = 0; i < cookiesLen; i++){
          var cookie = $.trim(cookies[i]);
          if (cookie.substring(0, name.length + 1) == (name + '=')){
            result = decodeURIComponent(cookie.substring(name.length + 1));
            break;
          }
        }
      }
    }
    return result;
  }

  /**
   * Set a specific cookie value
   *
   * This function is based in jQuery Cookie plugin by Klaus Hartl
   *
   * @access private
   * @param name String with the cookie name
   * @param value String with the cookie value
   * @param settings Array with plugin settings to use
   * @return Boolean True in every case
   *
   */
  function SetCookie(name, value, settings){
    var s = settings;
    var expires = '';
    var nType = 'number';
    if(s.cookies.expires && (typeof s.cookies.expires
     == nType || s.cookies.expires.toUTCString)){
       var date = null;
       if(typeof s.cookies.expires == nType){
         date = new Date();
         date.setTime(date.getTime() + (s.cookies.expires*24*60*60*1000));
       }else{
         date = s.cookies.expires;
       }
       // use expires attribute, max-age is not supported by IE
       expires = '; expires=' + date.toUTCString();
    }
    var path = s.cookies.path ? '; path=' + s.cookies.path : '';
    var domain = s.cookies.domain ? '; domain=' + s.cookies.domain : '';
    var secure = s.cookies.secure ? '; secure' : '';
    document.cookie = [name, '=', encodeURIComponent(value),
     expires, path, domain, secure].join('');
    return true;
  }

  /**
   * Clean a Widget Id from a cookie
   *
   * We use this in some places, so, centralize here. We clean certain
   * related cookie: two of the plugins related cookies using the same
   * structure to save their data, and can be clean in the same way.
   *
   * A string with comma separated Widgets IDs is stored in this cookies,
   * and "clean a cookie" want to say: remove certain Widget ID from this
   * cookie, because this widget is now visible or extended.
   *
   * @access private
   * @param widgetId String with a Widget identifier
   * @param cookieName String with the cookie name
   * @param settings Array with plugin settings to use
   * @return Boolean True in every case
   *
   */
  function CleanCookie(widgetId, cookieName, settings){
    value = GetCookie(cookieName);
    if(value != null){
      if(value.indexOf(widgetId) != -1){
        value = value.replace(','+widgetId, '');
        value = value.replace(widgetId+',', '');
        value = value.replace(widgetId, '');
      }
      SetCookie(cookieName, value, settings);
    }
    return true;
  }

  /**
   * Update a Widget Id from a cookie
   *
   * We use this in some places, so, centralize here. We update certain
   * related cookie: two of the plugins related cookies using the same
   * structure to save their data, and can be update in the same way.
   *
   * A string with comma separated Widgets IDs is stored in this cookies,
   * and "update a cookie" want to say: put certain Widget ID in this
   * cookie, because this widget is now closed or collapsed.
   *
   * @access private
   * @param widgetId String with a Widget identifier
   * @param cookieName String with the cookie name
   * @param settings Array with plugin settings to use
   * @return Boolean True in every case
   *
   */
  function UpdateCookie(widgetId, cookieName, settings){
    var value = GetCookie(cookieName);
    if(!value){
      value = widgetId;
    }else if(value.indexOf(widgetId) == -1){
      value = value+','+widgetId;
    }
    SetCookie(cookieName, value, settings);
    return true;
  }

  /**
   * Auxiliar function to prepare Widgets header menu links.
   *
   * @access private
   * @param text Link text
   * @param title Link title
   * @param aClass CSS class (behaviour) of link
   * @return String HTML of the link
   *
   */
  function MenuLink(text, title, aClass){
    var link = '<a href="#" title="TITLE" class="CLASS">TEXT</a>';
    link = link.replace(/TEXT/g, text);
    link = link.replace(/TITLE/g, title);
    link = link.replace(/CLASS/g, aClass.replace(/\./, ''));
    return link;
  }

  /**
   * Auxiliar function to show, hide and apply effects.
   *
   * @access private
   * @param jqObj jQuery object to apply the effect and show or hide
   * @param effect String that identifier what effect must be applied
   * @param duration Miliseconds to the effect duration
   * @param show Boolean True if want to show the object, False to be hide
   * @return Boolean True in every case
   * 
   */
  function ApplyEffect(jqObj, effect, duration, show){
    var n = 'none', f = 'fade', s = 'slide';
    if(!show){
      if(effect == n){
        jqObj.hide();
      }else if(effect == f){
        jqObj.fadeOut(duration);
      }else if(effect == s){
        jqObj.slideUp(duration);
      }
    }else{
      if(effect == n){
        jqObj.show();
      }else if(effect == f){
        jqObj.fadeIn(duration);
      }else if(effect == s){
        jqObj.slideDown(duration);
      }
    }
    return true;
  }

})(jQuery);