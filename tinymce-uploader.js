tinymce.PluginManager.add("responsivefilemanager", function(e) {
    function n(t) {
        0 === e.settings.external_filemanager_path.toLowerCase().indexOf(t.origin.toLowerCase()) 
		     && "responsivefilemanager" === t.data.sender 
			 && (tinymce.activeEditor.insertContent(t.data.html), 
			 tinymce.activeEditor.windowManager.close(), window.removeEventListener 
			 ? window.removeEventListener("message", n, !1) : window.detachEvent("onmessage", n))
    }

    function t() {
        e.focus(!0);
        var t = "RESPONSIVE FileManager";
        "undefined" != typeof e.settings.filemanager_title && e.settings.filemanager_title && (t = e.settings.filemanager_title);
        var i = "key";
        "undefined" != typeof e.settings.filemanager_access_key && e.settings.filemanager_access_key && (i = e.settings.filemanager_access_key);
        var a = "";
        "undefined" != typeof e.settings.filemanager_sort_by && e.settings.filemanager_sort_by && (a = "&sort_by=" + e.settings.filemanager_sort_by);
        var s = "false";
        "undefined" != typeof e.settings.filemanager_descending && e.settings.filemanager_descending && (s = e.settings.filemanager_descending);
        var r = "";
        "undefined" != typeof e.settings.filemanager_subfolder && e.settings.filemanager_subfolder && (r = "&fldr=" + e.settings.filemanager_subfolder);
        var o = "";
        "undefined" != typeof e.settings.filemanager_crossdomain && e.settings.filemanager_crossdomain && (o = "&crossdomain=1", window.addEventListener ? window.addEventListener("message", n, !1) : window.attachEvent("onmessage", n)), win = e.windowManager.open({
            title: t,
            file: e.settings.external_filemanager_path + "dialog.php?type=4&descending=" + s + a + r + o + "&lang=" + e.settings.language + "&akey=" + i,
            width: 860,
            height: 570,
            inline: 1,
            resizable: !0,
            maximizable: !0
        })
    }
    e.addButton("responsivefilemanager", {
        icon: "browse",
        tooltip: "Insert file",
        shortcut: "Ctrl+E",
        onclick: t
    }), e.addShortcut("Ctrl+E", "", t), e.addMenuItem("responsivefilemanager", {
        icon: "browse",
        text: "Insert file",
        shortcut: "Ctrl+E",
        onclick: t,
        context: "insert"
    })
});