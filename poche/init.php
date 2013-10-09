<?php
require_once "config.php";

class Poche extends Plugin {
  private $link;
  private $host;

  function about() {
    return array("1.7.9",
      "Poche (http://www.inthepoche.com/)",
      "jc.saaddupuy");
  }

  function init($host) {
    $this->host = $host;
    $this->dbh = $host->get_dbh();

    $host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
    $host->add_hook($host::HOOK_PREFS_TAB, $this);
  }

  function save() {
    $poche_url = db_escape_string($_POST["poche_url"]);
    $this->host->set($this, "poche", $poche_url);
    echo "Value set to $poche_url";
  }

  function get_js() {
    return file_get_contents(dirname(__FILE__) . "/poche.js");
  }

  function hook_prefs_tab($args) {
    if ($args != "prefPrefs") return;

    print "<div dojoType=\"dijit.layout.AccordionPane\" title=\"".__("Poche")."\">";

    print "<br/>";

    $value = $this->host->get($this, "poche");
    print "<form dojoType=\"dijit.form.Form\">";

    print "<script type=\"dojo/method\" event=\"onSubmit\" args=\"evt\">
      evt.preventDefault();
    if (this.validate()) {
      console.log(dojo.objectToQuery(this.getValues()));
      new Ajax.Request('backend.php', {
        parameters: dojo.objectToQuery(this.getValues()),
          onComplete: function(transport) {
            notify_info(transport.responseText);
  }
  });
  }
           </script>";

    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"op\" value=\"pluginhandler\">";
    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"method\" value=\"save\">";
    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"plugin\" value=\"poche\">";
    print "<table width=\"100%\" class=\"prefPrefsList\">";
        print "<tr><td width=\"40%\">".__("Poche url")."</td>";
        print "<td class=\"prefValue\"><input dojoType=\"dijit.form.ValidationTextBox\" required=\"1\" name=\"poche_url\" regExp='^(http|https)://.*' value=\"$value\"></td></tr>";
    print "</table>";
    print "<p><button dojoType=\"dijit.form.Button\" type=\"submit\">".__("Save")."</button>";

    print "</form>";

    print "</div>"; #pane

  }

  function hook_article_button($line) {
    return "<img src=\"plugins/poche/poche.png\"
             style=\"cursor : pointer\" style=\"cursor : pointer\"
             onclick=\"poche(".$line["id"].")\"
             class='tagsPic' title='".__('Bookmark on poche')."'>";
  }

  function inthepoche() {
    $id = db_escape_string($_REQUEST['id']);

    $result = $this->dbh->query("SELECT title, link
                      FROM ttrss_entries, ttrss_user_entries
                      WHERE id = '$id' AND ref_id = id AND owner_uid = " .$_SESSION['uid']);

    if (db_num_rows($result) != 0) {
      $title = truncate_string(strip_tags(db_fetch_result($result, 0, 'title')),
                               100, '...');
      $article_link = db_fetch_result($result, 0, 'link');
    }

    $poche_url = $this->host->get($this, "poche");

    print json_encode(array("link" => $article_link,
                            "id" => $id, "poche_url" => $poche_url));
  }
   function api_version() {
         return 2;
      }
}
?>
