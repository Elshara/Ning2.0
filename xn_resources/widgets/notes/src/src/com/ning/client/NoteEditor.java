package com.ning.client;

import com.google.gwt.i18n.client.Dictionary;
import com.google.gwt.core.client.GWT;
import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.ui.*;
import com.google.gwt.user.client.Window;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class NoteEditor implements EntryPoint {
	/**
	 * This {@link ImageBundle} is used for all the button icons. Using an image
	 * bundle allows all of these images to be packed into a single image, which
	 * saves a lot of HTTP requests, drastically improving startup time.
	 */
	protected interface Images extends ImageBundle {
		/**
		 * @gwt.resource bold.gif
		 */
		AbstractImagePrototype bold();

		/**
		 * @gwt.resource createLink.gif
		 */
		AbstractImagePrototype createLink();

		/**
		 * @gwt.resource createNoteLink.gif
		 */
		AbstractImagePrototype createNoteLink();
		
		/**
		 * @gwt.resource hr.gif
		 */
		AbstractImagePrototype hr();

		/**
		 * @gwt.resource indent.gif
		 */
		AbstractImagePrototype indent();

		/**
		 * @gwt.resource insertImage.gif
		 */
		AbstractImagePrototype insertImage();

		/**
		 * @gwt.resource italic.gif
		 */
		AbstractImagePrototype italic();

		/**
		 * @gwt.resource justifyCenter.gif
		 */
		AbstractImagePrototype justifyCenter();

		/**
		 * @gwt.resource justifyLeft.gif
		 */
		AbstractImagePrototype justifyLeft();

		/**
		 * @gwt.resource justifyRight.gif
		 */
		AbstractImagePrototype justifyRight();

		/**
		 * @gwt.resource ol.gif
		 */
		AbstractImagePrototype ol();

		/**
		 * @gwt.resource outdent.gif
		 */
		AbstractImagePrototype outdent();

		/**	
		 * @gwt.resource removeFormat.gif
		 */
		AbstractImagePrototype removeFormat();

		/**
		 * @gwt.resource removeLink.gif
		 */
		AbstractImagePrototype removeLink();

		/**
		 * @gwt.resource strikeThrough.gif
		 */
		AbstractImagePrototype strikeThrough();

		/**
		 * @gwt.resource ul.gif
		 */
		AbstractImagePrototype ul();

		/**
		 * @gwt.resource underline.gif
		 */
		AbstractImagePrototype underline();
	}

	protected class EventListener implements ClickListener, ChangeListener, KeyboardListener {
		public void onChange(Widget sender) {
			if (sender == fonts) {
				basic.setFontName(fonts.getValue(fonts.getSelectedIndex()));
				fonts.setSelectedIndex(0);
			} else if (sender == fontSizes) {
				basic.setFontSize(fontSizesConstants[fontSizes.getSelectedIndex() - 1]);
				fontSizes.setSelectedIndex(0);
			} else {
				return;
			}
			// mark document as modified
		}

		public void onClick(Widget sender) {
			if (sender == bold) { basic.toggleBold(); } 
			else if (sender == italic) { basic.toggleItalic(); } 
			else if (sender == underline) { basic.toggleUnderline(); } 
			else if (sender == strikeThrough) { extended.toggleStrikethrough(); } 
			else if (sender == indent) { extended.rightIndent(); } 
			else if (sender == outdent) { extended.leftIndent(); } 
			else if (sender == justifyLeft) { basic.setJustification(RichTextArea.Justification.LEFT); } 
			else if (sender == justifyCenter) { basic.setJustification(RichTextArea.Justification.CENTER); } 
			else if (sender == justifyRight) { basic.setJustification(RichTextArea.Justification.RIGHT); } 
			else if (sender == img) { onInsertImage(); return; } 
			else if (sender == link) { onInsertLink (); return; } 
			else if (sender == notelink) { onInsertNoteLink(); return; } 
			else if (sender == unlink) { extended.removeLink(); } 
			else if (sender == hr) { extended.insertHorizontalRule(); } 
			else if (sender == ol) { extended.insertOrderedList(); } 
			else if (sender == ul) { extended.insertUnorderedList(); } 
			else if (sender == unformat) { extended.removeFormat(); } 
			else if (sender == editor) { 
				updateStatus();
			}
			// mark document as modified
		}
		public void onKeyDown(Widget sender, char keyCode, int modifiers) { }

		public void onKeyPress(Widget sender, char keyCode, int modifiers) { }

		public void onKeyUp(Widget sender, char keyCode, int modifiers) { 
			// We use the RichTextArea's onKeyUp event to update the toolbar status.
			// This will catch any cases where the user moves the cursor using the
			// keyboard, or uses one of the browser's built-in keyboard shortcuts.
			if (sender == editor) {
				updateStatus();
			}
		}
	}

	Dictionary lang;
	RichTextArea editor;
	VerticalPanel toolbar;
	RichTextArea.BasicFormatter basic;
	RichTextArea.ExtendedFormatter extended;
	ToggleButton bold, italic, underline, strikeThrough;
	PushButton justifyLeft, justifyRight, justifyCenter, indent, outdent, 
				hr, ol, ul, img, notelink, link, unlink, unformat;
	ListBox fonts, fontSizes;
	RichTextArea.FontSize[] fontSizesConstants = new RichTextArea.FontSize[] {
			RichTextArea.FontSize.XX_SMALL, RichTextArea.FontSize.X_SMALL,
			RichTextArea.FontSize.SMALL, RichTextArea.FontSize.MEDIUM,
			RichTextArea.FontSize.LARGE, RichTextArea.FontSize.X_LARGE,
			RichTextArea.FontSize.XX_LARGE };
	EventListener listener;

	/**
	 * This is the entry point method.
	 */
	public void onModuleLoad() {
		lang = Dictionary.getDictionary("notesStrings");
		listener = new EventListener();
		createRichTextArea();
		RootPanel.get("noteEditorToolbar").add(toolbar);
		RootPanel.get("noteEditor").add(editor);
		initRichTextArea(this);
	}

	void createRichTextArea() {
		// Editor
		editor = new RichTextArea();
		editor.setHeight("30em");
		editor.setWidth("100%");

		toolbar = new VerticalPanel();
		Images images = (Images) GWT.create(Images.class);
		HorizontalPanel topPanel = new HorizontalPanel();
		HorizontalPanel bottomPanel = new HorizontalPanel();
		toolbar.add(topPanel);
		toolbar.add(bottomPanel);
		
		// Toolbars
		basic = editor.getBasicFormatter();
		extended = editor.getExtendedFormatter();

		if (basic != null) {
			topPanel.add(bold = createToggleButton(images.bold(),"TOGGLE_BOLD"));
			topPanel.add(italic = createToggleButton(images.italic(), "TOGGLE_ITALIC"));
			topPanel.add(underline = createToggleButton(images.underline(), "TOGGLE_UNDERLINE"));
			topPanel.add(justifyLeft = createPushButton(images.justifyLeft(), "JUSTIFY_LEFT"));
			topPanel.add(justifyCenter = createPushButton(images.justifyCenter(), "JUSTIFY_CENTER"));
			topPanel.add(justifyRight = createPushButton(images.justifyRight(), "JUSTIFY_RIGHT"));
			bottomPanel.add(fonts = createFontList());
			bottomPanel.add(fontSizes = createFontSizes());
			// We only use these listeners for updating status, so don't hook them up
			// unless at least basic editing is supported.
			editor.addKeyboardListener(listener);
			editor.addClickListener(listener);
		}
		if (extended != null) {
			topPanel.add(strikeThrough = createToggleButton(images.strikeThrough(), "TOGGLE_STRIKETHROUGH"));
			topPanel.add(indent = createPushButton(images.indent(), "INDENT_LEFT"));
			topPanel.add(outdent = createPushButton(images.outdent(), "INDENT_RIGHT"));
			topPanel.add(hr = createPushButton(images.hr(), "INSERT_HR"));
			topPanel.add(ol = createPushButton(images.ol(), "INSERT_OL"));
			topPanel.add(ul = createPushButton(images.ul(), "INSERT_UL"));
			topPanel.add(img = createPushButton(images.insertImage(), "INSERT_IMAGE"));
			topPanel.add(notelink = createPushButton(images.createNoteLink(), "CREATE_NOTELINK"));
			topPanel.add(link = createPushButton(images.createLink(), "CREATE_LINK"));
			topPanel.add(unlink = createPushButton(images.removeLink(), "REMOVE_LINK"));
			topPanel.add(unformat = createPushButton(images.removeFormat(), "REMOVE_FORMATTING"));
		}
	}

	public native void initRichTextArea(NoteEditor self) /*-{
		var ed = this.@com.ning.client.NoteEditor::editor;
		var w = $wnd.notes;
		var ext = this.@com.ning.client.NoteEditor::extended;
		var fn = this.@com.ning.client.NoteEditor::fonts;
		var fs = this.@com.ning.client.NoteEditor::fontSizes;
		w.editorGetText = function() { return ed.@com.google.gwt.user.client.ui.RichTextArea::getHTML()() };
		w.editorSetText = function(text) { 
			ed.@com.google.gwt.user.client.ui.RichTextArea::setHTML(Ljava/lang/String;)(text);
			self.@com.ning.client.NoteEditor::updateStatus()();
		};
		w.editorInsertImage = function(url) { ext.@com.google.gwt.user.client.ui.RichTextArea.ExtendedFormatter::insertImage(Ljava/lang/String;)(url); };
		w.editorCreateLink = function(url) { ext.@com.google.gwt.user.client.ui.RichTextArea.ExtendedFormatter::createLink(Ljava/lang/String;)(url); };
		w.editorDisableToolbar = function() { 
			fn.@com.google.gwt.user.client.ui.FocusWidget::setEnabled(Z)(false);
			fs.@com.google.gwt.user.client.ui.FocusWidget::setEnabled(Z)(false);
		};
		w.editorEnableToolbar = function() { 
			fn.@com.google.gwt.user.client.ui.FocusWidget::setEnabled(Z)(true);
			fs.@com.google.gwt.user.client.ui.FocusWidget::setEnabled(Z)(true);
		};
		w.editorSetText(w.savedContent);
		w.componentIsReady(0);
	}-*/;
	
	native void onInsertImage() /*-{
		$wnd.notes.widgetInsertImage();
	}-*/;
	native void onInsertLink() /*-{
		$wnd.notes.widgetInsertLink();
	}-*/;
	native void onInsertNoteLink() /*-{
		$wnd.notes.widgetInsertNoteLink();
	}-*/;

	ListBox createFontList() {
		ListBox lb = new ListBox();
		lb.addChangeListener(listener);
		lb.setVisibleItemCount(1);
		lb.addItem(lang.get("FONT"), "");
		lb.addItem("Andale Mono");
		lb.addItem("Arial Black");
		lb.addItem("Comics Sans");
		lb.addItem("Courier");
		lb.addItem("Futura");
		lb.addItem("Georgia");
		lb.addItem("Gill Sans");
		lb.addItem("Helvetica");
		lb.addItem("Impact");
		lb.addItem("Lucida");
		lb.addItem("Times New Roman");
		lb.addItem("Trebuchet");
		lb.addItem("Verdana");
		return lb;
	}
	ListBox createFontSizes() {
		ListBox lb = new ListBox();
		lb.addChangeListener(listener);
		lb.setVisibleItemCount(1);

		lb.addItem(lang.get("SIZE"));
		lb.addItem(lang.get("XXSMALL"));
		lb.addItem(lang.get("XSMALL"));
		lb.addItem(lang.get("SMALL"));
		lb.addItem(lang.get("MEDIUM"));
		lb.addItem(lang.get("LARGE"));
		lb.addItem(lang.get("XLARGE"));
		lb.addItem(lang.get("XXLARGE"));
		return lb;
	}
	
	PushButton createPushButton(AbstractImagePrototype img, String tip) {
		PushButton pb = new PushButton(img.createImage());
		pb.addClickListener(listener);
		pb.setTitle(lang.get(tip));
		return pb;
	}

	ToggleButton createToggleButton(AbstractImagePrototype img, String tip) { 
		ToggleButton tb = new ToggleButton(img.createImage());
		tb.addClickListener(listener);
		tb.setTitle(lang.get(tip));
		return tb;
	}
	/**
	 * Updates the status of all the stateful buttons.
	 */
	void updateStatus() {
		if (basic != null) {
			bold.setDown(basic.isBold());
			italic.setDown(basic.isItalic());
			underline.setDown(basic.isUnderlined());
		}
		if (extended != null) {
			strikeThrough.setDown(extended.isStrikethrough());
		}
	}
}
