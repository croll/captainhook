/*
    Script: UI.Tabs.js

    Class: UI.Tabs
        Creates Tabs

    Syntax:
        >var myTabs = new UI.Tabs( [options] );

    Arguments:
        options - (object, optional) An object with options for the tabs. See below.

        options (continued):
            id          - (string: defaults to Native.UID++) The id of the tabs element.
            container   - (string: defaults to document.body) The container for the tabs.
            clsPrefix   - (string: defaults to 'ui-') The class prefix for CSS modifications
            panelWidth  - (string: defaults to 100%) The width of the panels.
            panelHeight - (string: defaults to 100%) The height of the panels.
            scrolling   - (string/boolean: defaults to 'auto') Enable tabs scrolling if there is more tabs than space allowed. Possible values : 'auto' / true / false
            sortable    - (boolean: defaults to false) Enable tabs sort.
            contextMenu - (boolean: defaults to false) Context menu is shown when user right clicks on a tab. UI.Menu class must be included.

    Returns:
        (class)  A new UI.Tabs class instance.

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
        [/javascript]
*/
UI.Tabs= new Class ({

    Implements : [ Options, Events ],

    options : {
            container   : null
        ,   id          : null
        ,   clsPrefix   : 'ui-'
        ,   scrolling   : 'auto'
        ,   sortable    : false
        ,   contextMenu : false
    },

    initialize: function( options ) {
        this.tabs   = [];
        this.panels = [];
        this.showScroll = false;
        this.activeTab  = false;
        this.setOptions( options );
        this.container      = $( $pick( this.options.container, document.body ) );
        this.id             = $pick( this.options.id, 'tabs-' + (Native.UID++) );

        this.bigContainer   = new Element( 'div', { 'id' : this.id, 'class' : this.options.clsPrefix + 'tabs' } ).inject( this.options.container, 'inside' );
        this.tabsContainer  = new Element( 'div', { 'class' : this.options.clsPrefix + 'tabs-container' } ).inject( this.bigContainer, 'inside' ).adopt( new Element( 'ul' ) );
        this.panelsContainer = new Element( 'div', { 'class' : this.options.clsPrefix + 'tabs-panels' } ).inject( this.bigContainer, 'inside' );

        this.scrollerLeft   = new Element( 'div', { 'class' : this.options.clsPrefix + 'scroller-left' } ).inject( this.tabsContainer, 'inside' );

        this.scrollerLeft.addEvents( {      'mouseenter' : function() { this.addClass( 'over' ); }
                                        ,   'mouseleave' : function() { this.removeClass( 'over' ); }
                                        ,   'click' : function() { this.scrollPrevious(); }.bind(this)
        } );

        this.scrollerRight   = new Element( 'div', { 'class' : this.options.clsPrefix + 'scroller-right' } ).inject( this.tabsContainer, 'inside' )

        this.scrollerRight.addEvents( {     'mouseenter' : function() { this.addClass( 'over' ); }
                                        ,   'mouseleave' : function() { this.removeClass( 'over' ); }
                                        ,   'click' : function() { this.scrollNext(); }.bind(this)
        } );

        if ( this.options.scrolling == true ) this.showScrolling();

        if ( this.options.sortable == true ) {
            this.sortables  = new Sortables( this.tabsContainer.getElement( 'ul' ), { revert: { duration: 300 } } );
        };

        this.refreshSize();

        $(this.id).store( 'tabs-obj', this );

    },

/*
    Method: addTab
        Adds a tab

    Syntax:
        >myTabs.addTab( options );

    Arguments:
        options - (object, optional) An object with options for tabs. See below.

        options (continued):
            id          - (string: defaults to Native.UID++) The id of the tab element.
            label       - (string: defaults to false) The label of the tab.
            icon        - (string: defaults to false) The path to the icon of the tab.
            active      - (boolean: defaults to false) True if the tab is the active tab.
            status      - (boolean: defaults to true) True if the tab is enable.
            closable    - (boolean: defaults to false) True if the tab is closable.
            content     - (string: defaults to false) Text/html to insert into panel.

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
        [/javascript]
*/
    addTab: function( tab ) {

        tab     = $merge( { closable    :   false
                        ,   status      :   true
                        ,   active      :   false
                        ,   content     :   false
                        ,   id          :   null }, tab );

        tab.id = $pick( tab.id, 'ui-tab-' + Native.UID++ );

        // Tab creation
        this.tabs.push( tab.id );
        this.tabs[ tab.id ] = tab;
        this.element    = new Element( 'li', { id : tab.id, 'class' : tab.active ? this.options.clsPrefix + 'tab active' : this.options.clsPrefix + 'tab' } )
                            .adopt( new Element( 'span', { 'class' : 'left' } ).adopt( new Element( 'span', { 'class' : 'right' } ).adopt( new Element( 'span', { 'class' : 'middle' } ).adopt( new Element( 'span', { 'class' : 'text' } ) ) ) ) );

        if ( tab.closable ) {
            this.element.addClass( 'closable' );
            this.element.adopt( new Element( 'span', { 'class' : 'ui-tab-btn-close' } ).setOpacity(0.6) );
            this.element.getElement(' span.ui-tab-btn-close' ).addEvents( {
                                            'mouseenter' : function() { this.setOpacity(1); }
                                        ,   'mouseleave' : function() { this.setOpacity(0.6); }
                                        ,   'click'      : function( event, el ) {
                                                                    event.stopPropagation();
                                                                    this.removeTab( el.get( 'id' ) );
                                                            }.bindWithEvent( this, this.element )
            } );
        };

        if ( tab.icon ) this.element.getElement( 'span.text' ).adopt( new Element( 'img', { src : tab.icon } ) );
        if ( tab.label ) this.element.getElement( 'span.text' ).appendText( tab.label );

        this.element.addEvents( {   'mouseenter' : function() { this.addClass( 'over' ); }
                                ,   'mouseleave' : function() { this.removeClass( 'over' ); }
        } );

        if ( this.options.contextMenu && $defined( UI.Menu ) ) {
            this.tabs[ tab.id ].menu = new UI.Menu( this.element, { event : 'rightClick', position : [ 'bottom', 'left' ] } );
            if ( tab.closable ) this.tabs[ tab.id ].menu.addItem( { label :'Close', onclick : function(id) { this.removeTab(id); }.pass( tab.id, this) } );
            this.tabs[ tab.id ].menu.addItem( { label :'Close others', onclick : function(id) { this.removeOthers(id); }.pass( tab.id, this) } );
        };

        this.element.addEvent( 'click', function() { this.activateTab( tab.id ); }.bind(this) );
        this.element.inject( this.tabsContainer.getElement( 'ul' ), 'inside' );
        this.tabs[ tab.id ].element = this.element;

        // Cleaning
        delete this.element;

        // Panel creation
        this.panels.push( 'panel-' + tab.id );
        this.panels[ 'panel-' + tab.id ] = {};
        this.element = new Element( 'div', { id : 'panel-' + tab.id, 'class' : this.options.clsPrefix + 'tab-panel' } );
        if (tab.content) {
            if (tab.content.inject)
                tab.content.inject(this.element);
            else
                this.element.set( 'html', tab.content );
        }
        this.element.inject( this.panelsContainer, 'inside' );
        this.element.setStyles( {   'height' : this.panelsContainer.getSize().y //this.options.panelHeight
                                ,   'width' : this.panelsContainer.getSize().x //this.options.panelWidth
                                } );
//        if ( tab.panelClass ) this.element.addClass( tab.panelClass );
        this.panels[ 'panel-' + tab.id ].element = this.element;

        // Cleaning
        delete this.element;

        if ( this.options.scrolling == 'auto' && !tab.active ) this.refreshScroll.delay( 15, this );
        if ( this.options.sortable == true ) this.sortables.addItems( this.tabs[ tab.id ].element );
        if ( tab.active ) this.activateTab( tab.id );

    },

/*
    Method: addTabs
        Adds tabs

    Syntax:
        >myTabs.addTabs( [ options[, options, ...] ] );

    Arguments:
        options - (object, optional) An object with options for tabs. See addTab options.

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTabs(     [   { label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab 1 clicked') } }
                            ,       { label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab 2 clicked') } } ] );
        [/javascript]
*/
    addTabs: function( tabs ) {
        if ( tabs.length > 0 ) {
            $each( tabs, function( tab ) { this.addTab( tab ); }, this );
        };
    },

/*
    Method: removeTab
        Removes a tab

    Syntax:
        >myTabs.removeTab( el );

    Arguments:
        el      - (string or number) ID or index of the tab

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
            myTabs.removeTab( 'tab1' ); // or myTabs.removeTab( 0 );
        [/javascript]
*/
    removeTab: function( ref ) {
        var id  = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;

        if ( this.activeTab == id ) this.shiftTab( id );

        if ( this.options.sortable == true ) this.sortables.removeItems( this.tabs[ id ].element );

        this.tabs[ id ].element.destroy();
        this.tabs[ id ] = undefined;
        this.tabs.erase( id );

        this.panels[ 'panel-' + id ].element.destroy();
        this.panels[ 'panel-' + id ]    = undefined;
        this.panels.erase( 'panel-' + id );

        if ( this.showScroll ) this.refreshScroll.delay( 20, this );

        this.fireEvent('close', {
            id: id
        });
    },

/*
    Method: removeOthers
        Removes all tabs except arg

    Syntax:
        >myTabs.removeOthers( ref );

    Arguments:
        ref     - (string or number) ID or index of the tab

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
            myTabs.removeTab( 'tab1' ); // or myTabs.removeTab( 0 );
        [/javascript]
*/
    removeOthers: function( ref ) {
        var id  = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;
        this.activateTab( id );
        var toClose = this.tabs.filter( function( item ) { return (item != this.activeTab && this.tabs[ item ].closable); }, this );
        toClose.forEach( function( item ) { this.removeTab( item ); }, this );
        this.hideScrolling();
    },

/*
    Method: activateTab
        Activate a tab

    Syntax:
        >myTabs.activateTab( ref );

    Arguments:
        ref     - (string or number) ID or index of the tab

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
            myTabs.activateTab( 'tab1' ); // or myTabs.activateTab( 0 );
        [/javascript]
*/
    activateTab: function( ref ) {
        var id  = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;

        if ( this.activeTab != id ) {

            this.activeTab = id;

            this.tabs.each( function(id) { this.tabs[id].element.removeClass( 'active' ); }.bind(this) );
            this.panels.each( function(id) { this.panels[id].element.removeClass( 'visible' ); }.bind(this) );

            this.tabs[ id ].element.addClass( 'active' );
            this.panels[ 'panel-' + id ].element.addClass( 'visible' );

            this.refreshScroll(); //.delay( 15, this );
        };
    },

    fillPanel: function( id, content ) {
        this.panels[ 'panel-' + id ].element.set( 'html', content );
    },

    getPanelElement: function( id ) {
        return this.panels[ 'panel-' + id ].element;
    },

/*
    Method: enableTab
        Enable a tab

    Syntax:
        >myTabs.enableTab( ref );

    Arguments:
        ref     - (string or number) ID or index of the tab

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
            myTabs.enableTab( 'tab1' ); // or myTabs.enableTab( 0 );
        [/javascript]
*/
    enableTab: function( ref ) {
        var id  = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;
        this.tabs[ id ].element.addEvent( 'click', function() { this.activateTab( id ); }.bind(this) );
        this.tabs[ id ].element.removeClass( 'disable' ).setOpacity( 1 );
    },

/*
    Method: disableTab
        Disable a tab

    Syntax:
        >myTabs.disableTab( ref );

    Arguments:
        ref     - (string or number) ID or index of the tab

    Example:
        [javascript]
            var myTabs = new UI.Tabs();
            myTabs.addTab( { id : 'tab1', label : 'Demo Tab', icon : 'images/demo.png', onclick : function() { alert('demo tab clicked') } } );
            myTabs.disableTab( 'tab1' ); // or myTabs.disableTab( 0 );
        [/javascript]
*/
    disableTab: function( ref ) {
        var id  = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;
        if ( this.activeTab == id ) this.shiftTab();

        this.tabs[ id ].element.removeEvents();
        this.tabs[ id ].element.addClass( 'disable' ).setOpacity( 0.4 );
    },

    refreshSize: function() {

//        var containerX = 0;
//        new Hash( this.container.getStyles( 'border-right-width', 'border-left-width' ) ).getValues().forEach( function( prop ) { alert( prop + ' / ' + prop.toInt() );containerX += prop.toInt(); } );
//        alert( containerX);

        this.bigContainer.setStyles( {      'width' : this.container.getSize().x - this.container.getStyle( 'border-right-width' ).toInt() - this.container.getStyle( 'border-left-width' ).toInt()
                                        ,   'height': this.container.getSize().y - this.container.getStyle( 'border-top-width' ).toInt() - this.container.getStyle( 'border-bottom-width' ).toInt()
        } );

        this.tabsContainer.setStyles( {     'width' : this.bigContainer.getSize().x } );

        this.panelsContainer.setStyles( {   'width' : this.bigContainer.getSize().x
                                        ,   'height': this.bigContainer.getSize().y - this.tabsContainer.getSize().y
        } );

        this.panels.each( function( panel ) { panel.setStyles( { 'width' : this.panelsContainer.getSize().x, 'height' : this.panelsContainer.getSize().y } ); }.bind(this) );

        if ( this.scrollShow ) this.refreshScroll();

    },

    refreshScroll: function( id ) {
        var id  = $pick( id, this.activeTab );

        if ( !this.showScroll && this.options.scrolling == 'auto' && ( this.tabs[ this.tabs.getLast() ].element.getCoordinates().right - this.tabs[ this.tabs[0] ].element.getCoordinates().left ) > this.bigContainer.getSize().x ) this.showScrolling();
        if ( this.showScroll && this.options.scrolling == 'auto' && ( this.tabs[ this.tabs.getLast() ].element.getCoordinates().right - this.tabs[ this.tabs[0] ].element.getCoordinates().left ) < this.bigContainer.getSize().x ) this.hideScrolling();

        if ( this.showScroll ) {
            if ( this.tabs[ this.tabs.getLast() ].element.getCoordinates().right < this.scrollerRight.getCoordinates().left - 3 ) this.scrollTo( this.tabs[ this.tabs.getLast() ].id, 'right' );
            if ( this.tabs[ id ].element.getCoordinates().left < this.scrollerLeft.getCoordinates().right ) this.scrollTo( id, 'left' ); //this.scrollTo.delay( 15, this, [ id, 'left' ] );
            if ( this.tabs[ id ].element.getCoordinates().right > this.scrollerRight.getCoordinates().left ) this.scrollTo( id, 'right' ); //this.scrollTo.delay( 15, this, [ id, 'right' ] );
        };
    },

    scrollPrevious: function() {
        var previousTab = this.tabs.filter( function( item, index ) { return this.tabs[item].element.getCoordinates().left < this.scrollerLeft.getCoordinates().right }, this ).getLast();
        if ( $defined( previousTab ) ) this.scrollTo( previousTab, 'left' );
    },

    scrollNext: function() {
        var nextTab = this.tabs.filter( function( item, index ) { return this.tabs[item].element.getCoordinates().right > this.scrollerRight.getCoordinates().left }, this )[0];
        if ( $defined( nextTab ) ) this.scrollTo( nextTab, 'right' );
    },

    scrollTo: function( ref, align ) {
        var id      = ( ref == 'string' ) ? ref : this.tabs[ ref ].id;
        var tabEl   = this.tabs[ id ].element;
        var ulEl    = this.tabsContainer.getElement( 'ul' );
        if ( align == 'left' ) {
            this.tabsContainer.getElement( 'ul' ).tween( 'margin-left', ulEl.getStyle( 'margin-left' ).toInt() + ( this.scrollerLeft.getStyle( 'margin-right' ).toInt() + this.scrollerLeft.getCoordinates().right - tabEl.getStyle( 'margin-left' ).toInt() - tabEl.getCoordinates().left ) );
        } else {
            this.tabsContainer.getElement( 'ul' ).tween( 'margin-left', ulEl.getStyle( 'margin-left' ).toInt() + ( this.scrollerRight.getStyle( 'margin-left' ).toInt() + this.scrollerRight.getCoordinates().left - tabEl.getStyle( 'margin-right' ).toInt() - tabEl.getCoordinates().right ) );
        };
    },

    showScrolling: function() {
        this.scrollerLeft.addClass( 'visible' );
        this.scrollerRight.addClass( 'visible' );
        this.showScroll = true;
    },

    hideScrolling: function() {
        this.scrollerLeft.removeClass( 'visible' );
        this.scrollerRight.removeClass( 'visible' );
        this.tabsContainer.getElement( 'ul' ).setStyle( 'margin-left', '0px' );
        this.showScroll = false;
    },

    shiftTab: function( fromId ) {
        var toactive=this.tabs[ this.tabs[ ( this.tabs.indexOf( this.activeTab ) + 1 == this.tabs.length ? this.tabs.indexOf( this.activeTab ) - 1 : this.tabs.indexOf( fromId ) + 1 ) ] ];
        if (toactive)
            this.activateTab(toactive.id);
    }

} );
