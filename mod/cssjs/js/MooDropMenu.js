/*
---
description: This provides a simple Drop Down menu with infinit levels

license: MIT-style

authors:
- Arian Stolwijk
- Alexander Duschek

requires:
  - Core/Class.Extras
  - Core/Element.Event
  - Core/Selectors

provides: [MooDropMenu, Element.MooDropMenu]

accessibility enhancements: WAI ARIA specification (http://www.w3.org/TR/wai-aria-practices/#menu) is implemented, DropDown can be controlled completely by the keyboard.

...
	
*/

var MooDropMenu = new Class({

    Implements: [Options, Events],
    
    options: {
        onOpen: function(el){
            el.removeClass('close').addClass('open');
        },
        onClose: function(el){
            el.removeClass('open').addClass('close');
        },
        onInitialize: function(el){
            el.removeClass('open').addClass('close');
        },
        mouseoutDelay: 200,
        mouseoverDelay: 200,
        listSelector: 'ul',
        itemSelector: 'li',
        
        /* ##### modification start ###### */
        // saves the element that has the focus at the moment
        elementWithFocus: null,
        // timer variable
        timer: null,
        // counts the open submenus
        menuCount: 0,
        // saves the last menuitem with a submenu that was hovered
        lastItem: null,
        // saves the last menuitem that was the active item
        lastActive: null,
        // collection of all open menus
        menuArray: new Array(),
        // collection of all coloured items
        colorArray: new Array()
        /* ##### modification end ##### */
    },
    
    initialize: function(menu, options, level){
        this.setOptions(options);
        options = this.options;
        
        var menu = this.menu = document.id(menu);
        
        /* ##### modification start ###### */
        
        // set the role "menu"
        menu.setProperty('role', 'menu');
        
        // grab all items, set roles and listeners
        menu.getElements(options.itemSelector).each(function(element){
        
            // set the role
            element.setProperty('role', 'presentation');
            element.getChildren()[0].setProperty('role', 'menuitem');
            element.getChildren()[0].setProperty('tabindex', '-1');
            
            // listener on <div> element
            element.getChildren()[0].addEvents({
            
                'focus': function(event){
                
                    if (event.target != null) {
                        this.uncolorAll();
                        options.elementWithFocus = event.target;
                        this.recolor();
                    }
                    
                }
.bind(this)                ,
                
                'blur': function(){
                    options.elementWithFocus = null;
                }
.bind(this)                ,
                
                'mouseenter': function(event){
                    if (event.target != null) {
                        this.uncolorAll();
                        this.options.elementWithFocus = event.target;
                        this.recolor();
                        
                    }
                }
.bind(this)                ,
                
                'mouseleave': function(){
                    this.uncolorAll();
                }
.bind(this)
            
            });
            
        }
.bind(this));
        
        // first element must be tabbable
        menu.getElements(options.itemSelector)[0].getChildren()[0].setProperty('tabindex', '0');
        
        /* ##### modification end ##### */
        
        // grab all elements with submenus
        menu.getElements(options.itemSelector + ' > ' +
        options.listSelector).each(function(el){
        
            this.fireEvent('initialize', el);
            
            var parent = el.getParent(options.itemSelector);
            
            /* ##### modification start ###### */
            // set aria states
            parent.getChildren()[0].setProperty('aria-haspopup', 'true');
			var childs = new Element('span', {
                'class': 'arrow'
            }).inject(parent.getChildren()[0]);
            el.setProperty('role', 'menubar');
            el.setProperty('aria-activedescendant', 'activeItem');
            /* ##### modification end ##### */
            
            parent.getChildren().removeEvents('mouseenter');
            parent.getChildren().removeEvents('mouseleave');
            
            // listener on <li> element
            parent.addEvents({
            
                /*
                 * ##### modification start
                 * #####
                 */
                'mouseenter': function(event){
                
                    //console.log("mouseenter2");
                    if (event.target != null) {
                    
                        if (this.options.colorArray.length != 0) {
                            this.closeAll();
                        }
                        
                        var item = event.target;
                        while (item.get('tag') != 'li') {
                            item = item.getParent();
                        }
                        
                        this.options.elementWithFocus = item;
                        //console
                        //		.log(this.options.elementWithFocus);
                        
                        // open submenu
                        this.openSub(el, parent);
                        
                        this.recolor();
                    }
                    /*
                     * ##### modification end
                     * #####
                     */
                }
.bind(this)                ,
                /*
                 * ##### modification start
                 * #####
                 */
                'mouseleave': function(event){
                
                    this.uncolorAll();
                    this.closeSub(el, parent);
                    this.options.lastItem = null;
                    this.options.elementWithFocus = null;
                    /*
                     * ##### modification end
                     * #####
                     */
                }
.bind(this)                ,
                
                'click': function(){
                    return;
                }
            });
            
        }, this);
        
        /* ##### modification start #### */
        
        // grab all items in the menubar
        menu.getElements(options.itemSelector).each(function(element){
        
            element.getChildren()[0].addEvents({
                'keydown': function(event){
                    var keyCode;
                    if (window.event) {
                        var e = window.event;
                        keyCode = e.keyCode;
                    }
                    else {
                        keyCode = event.code;
                    }
                    
                    // look into the
                    // specification for the
                    // exact action
                    // of every key
                    switch (keyCode) {
                        case 9:
                            // tab
                            // closes all open
                            // submenus by
                            // iterating over
                            // all openend menus
                            // in the array
                            this.closeAll();
                            
                            break;
                        case 13:
                            // enter;
                            this.open();
                            break;
                        case 32:
                            // space
                            this.open();
                            break;
                        case 27:
                            // escape
                            this.close();
                            break;
                        case 37:
                            // left
                            event.stop();
                            if (this.options.menuCount == 0) {
                                this.prev();
                            }
                            else 
                                if (this.options.menuCount > 0) {
                                    this.close();
                                }
                            break;
                        case 38:
                            // up
                            event.stop();
                            if (this.options.menuCount == 0) {
                                this.open();
                            }
                            else 
                                if (this.options.menuCount > 0) {
                                    if (this.options.lastItem != null) {
                                        this.focChild();
                                        this.options.lastItem = null;
                                    }
                                    else {
                                        this.prev();
                                    }
                                }
                            break;
                        case 39:
                            // right
                            event.stop();
                            if (this.options.menuCount == 0) {
                                this.next();
                            }
                            else 
                                if (this.options.menuCount > 0) {
                                    this.open();
                                }
                            break;
                        case 40:
                            // down
                            event.stop();
                            if (this.options.menuCount == 0) {
                                this.open();
                            }
                            else 
                                if (this.options.menuCount > 0) {
                                    if (this.options.lastItem != null) {
                                    
                                        this.focLastChild();
                                        this.options.lastItem = null;
                                    }
                                    else {
                                        this.next();
                                    }
                                }
                            break;
                        default:
                            break;
                    }
                    
                }
.bind(this)
            });
        }
.bind(this));
    },
    
    /* ##### modification end ##### */
    
    toElement: function(){
        return this.menu;
    },
    
    /* ##### modification start ###### */
    // opens a submenu
    openSub: function(element, parent){
    
        parent.store('DropDownOpen', true);
        
        clearTimeout(this.options.timer);
        if (this.options.mouseoverDelay) {
            timer = this.fireEvent.delay(this.options.mouseoverDelay, this, ['open', element]);
        }
        else {
            this.fireEvent('open', element);
        }
        
        this.options.menuCount++;
        
    },
    
    // determines the element to open a menu or submenu
    // calls openSub() for actual open action
    open: function(){
        var el = null, parent = null;
        
        if (this.options.elementWithFocus != null) {
            if (this.options.elementWithFocus.getChildren('ul') != null) {
            
                parent = this.options.elementWithFocus.getParent();
                el = this.options.elementWithFocus.getNext();
                
                if ((el != null) && (parent != null)) {
                
                    this.uncolorAll();
                    this.options.menuArray.push(new Array(el, parent));
                    this.openSub(el, parent);
                    
                    // timeout is required to make the focus jump into a
                    // submenu, it doesn't work without it
                    window.setTimeout(function(){
                        this.focChild();
                    }
.bind(this), this.options.mouseoutDelay+50);
                    this.recolor();
                }
                
            }
        }
    },
    
    // closes a submenu
    closeSub: function(element, parent){
        parent.store('DropDownOpen', false);
        
        clearTimeout(this.options.timer);
        timer = (function(){
            if (!parent.retrieve('DropDownOpen')) {
                this.fireEvent('close', element);
            }
        }).delay(this.options.mouseoutDelay, this);
        
        if (this.options.menuCount > 0) 
            this.options.menuCount = this.options.menuCount - 1;
        
    },
    
    // determines the element to close a menu or submenu
    // calls closeSub() for actual close action
    close: function(){
        var el = null, parent = null;
        
        if (this.options.elementWithFocus != null) {
        
            if (this.options.menuArray.getLast() != null) {
            
                el = this.options.menuArray.getLast()[0];
                parent = this.options.menuArray.getLast()[1];
                
                if (el != null && parent != null) {
                
                    // remove css markup
                    this.uncolorAll();
                    // remove element from menuArray
                    this.options.menuArray.pop();
                    // focus the parent element
                    this.focParent();
                    // close the submenu
                    this.closeSub(el, parent);
                    // reset css markup
                    this.recolor();
                }
            }
        }
    },
    
    // closes all open submenus and removes optical markups
    closeAll: function(){
        this.options.menuArray.reverse();
        this.options.menuArray.each(function(item){
            this.closeSub(item[0], item[1]);
        }
.bind(this));
        
        this.uncolorAll();
        this.options.menuArray = new Array();
        this.options.menuCount = 0;
        this.options.elementWithFocus = null;
        this.options.lastItem = null;
    },
    
    // mark the next element with an <li> tag in the list
    // works for menubar und submenus
    next: function(){
    
        if (this.options.elementWithFocus != null) {
            this.uncolorAll();
            if (this.options.elementWithFocus.getParent().getNext() != null) {
            
                var item = this.options.elementWithFocus.getParent().getNext().getChildren()[0];
                item.focus();
                
            }
            else {
            
                var item = this.options.elementWithFocus.getParent().getParent().getChildren()[0].getChildren()[0];
                item.focus();
            }
            this.recolor();
        }
    },
    
    // mark the previous element with an <li> tag in the list
    // works for menubar und submenus
    prev: function(){
        if (this.options.elementWithFocus != null) {
            this.uncolorAll();
            if (this.options.elementWithFocus.getParent().getPrevious() != null) {
            
                var item = this.options.elementWithFocus.getParent().getPrevious().getChildren()[0];
                item.focus();
                
            }
            else {
            
                var x = this.options.elementWithFocus.getParent().getParent().getChildren().length;
                var item = this.options.elementWithFocus.getParent().getParent().getChildren()[x - 1].getChildren()[0];
                item.focus();
            }
            this.recolor();
        }
    },
    
    // marks the first element in a submenu as focused
    // elementWithFocus must be the <li> tag within a <ul> tag
    focChild: function(){
        if (this.options.elementWithFocus != null) {
        
            if (this.options.elementWithFocus.getNext().getChildren()[0] != null) {
            
                var item = this.options.elementWithFocus.getNext().getChildren()[0].getChildren()[0];
                item.focus();
            }
        }
    },
    
    // marks the last element in a submenu as focused
    // elementWithFocus must be the <li> tag within a <ul> tag
    focLastChild: function(){
        if (this.options.elementWithFocus != null) {
        
            var len = this.options.elementWithFocus.getNext().getChildren().length;
            
            var item = this.options.elementWithFocus.getNext().getChildren()[len - 1].getChildren()[0];
            
            item.focus();
        }
    },
    
    // marks the parent element of a submenu as focused
    // elementWithFocus must be the <li> tag within a <ul> tag
    focParent: function(){
        if (this.options.elementWithFocus != null) {
        
            var item = this.options.elementWithFocus.getParent().getParent().getPrevious();
            item.focus();
        }
    },
    
    // sets a special css class to show a recently focused element
    color: function(){
    
        var el = this.options.elementWithFocus;
        if (el != null) {
        
            while (el.get('tag') != 'li') {
                el = el.getParent();
            }
            
            el.addClass('focus');
            this.options.colorArray.push(el);
        }
    },
    
    // removes the special focus of all elements and of the active item
    uncolorAll: function(){
    
        this.options.colorArray.each(function(item){
            while (item.get('tag') != 'li') {
                item = item.getParent();
            }
            this.removeMarkups(item);
        }
.bind(this));
        this.options.colorArray = new Array();
        
    },
    
    // removes optical markups (used by "uncolorAll")
    removeMarkups: function(item){
    
        if (item != null) {
            item.removeClass('focus').removeClass('activeItem');
        }
    },
    
    // bottom-up function to color all <li> elements, add the class
    // focus to all elements
    recolor: function(){
        var item = this.options.elementWithFocus;
        this.setActive();
        
        this.options.colorArray = new Array();
        while (item.getProperty('role') != 'menu') {
            if (item.get('tag') == 'li') {
                item.addClass('focus');
                this.options.colorArray.push(item);
            }
            item = item.getParent();
        }
        
    },
    
    // marks an element as active via the id activeItem, style can be
    // changed via css
    setActive: function(){
        //console.log("setActive");
        var el = this.options.elementWithFocus;
        //console.log(el);
        
        if (el != null) {
        
            // highlight only menuitems
            while (el.get('tag') != 'li') {
                el = el.getParent();
            }
            
            // unmark the last element
            if (this.options.lastActive != null) {
                this.options.lastActive.removeClass('activeItem');
            }
            
            // save the new element
            this.options.lastActive = el.addClass('activeItem');
            
            //console.log(this.options.lastActive);
        }
    }
    /* ##### modification end ##### */
});

/*
 * So you can do like this $('nav').MooDropMenu(); or even
 * $('nav').MooDropMenu().setStyle('border',1);
 */
Element.implement({
    MooDropMenu: function(options){
        return this.store('MooDropMenu', new MooDropMenu(this, options));
    }
});
