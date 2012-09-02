{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
	<table class="form-layout">
		<tr>
            <td><label>{ts}Complete OR Partial Name{/ts}</label>&nbsp;{help id='id-advanced-intro'}<br />
                {$form.sort_name.html|crmReplace:class:big}
            </td>
            <td>
                <label>{ts}Complete OR Partial Email{/ts}</label><br />
                {$form.email.html|crmReplace:class:medium}
            </td>
            <td>
                {$form.uf_group_id.label} {help id="id-search-views"}<br />{$form.uf_group_id.html}
            </td>
            <td>
                {if $form.component_mode}
                    {$form.component_mode.label} {help id="id-display-results"}
                    <br />
                    {$form.component_mode.html}
{if $form.display_relationship_type}
            <span id="crm-display_relationship_type">{$form.display_relationship_type.html}</span>
{/if}
                {else}
                    &nbsp;
                {/if}
            </td>
            <td class="labels" rowspan="2">
                <div class="crm-submit-buttons">
                    {include file="CRM/common/formButtons.tpl" location="top" buttonStyle="width:80px; text-align:center;"}
                </div>
                <div class="crm-submit-buttons reset-advanced-search">
                    <a href="{crmURL p='civicrm/contact/search/advanced' q='reset=1'}" id="resetAdvancedSearch" class="button" style="width:70px; text-align:center;"><span>{ts}Reset Form{/ts}</span></a>
                </div>
            </td>
        </tr>
		<tr>
{if $form.contact_type}
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.contact_type.html}
                 {literal}
					<script type="text/javascript">

								cj("select#contact_type").crmasmSelect({
									addItemTarget: 'bottom',
									animate: false,
									highlight: true,
									sortable: true,
									respectParents: true
								});

						</script>
					{/literal}
            </td>
{else}
            <td>&nbsp;</td>
{/if}
{if $form.group}
            <td><label>{ts}Group(s){/ts}</label>
                {$form.group.html}
                {literal}
                <script type="text/javascript">
                cj("select#group").crmasmSelect({
                    addItemTarget: 'bottom',
                    animate: false,
                    highlight: true,
                    sortable: true,
                    respectParents: true
                });

                </script>
                {/literal}
            </td>
{else}
            <td>&nbsp;</td>
{/if}
            <td>{$form.operator.label} {help id="id-search-operator"}<br />{$form.operator.html}</td>
            <td>
                {if $form.deleted_contacts}{$form.deleted_contacts.html} {$form.deleted_contacts.label}{else}&nbsp;{/if}
            </td>
    </tr>
    <tr>
{if $form.contact_tags}
            <td><label>{ts}Select Tag(s){/ts}</label>
                {$form.contact_tags.html}
                {literal}
                <script type="text/javascript">

                cj("select#contact_tags").crmasmSelect({
                    addItemTarget: 'bottom',
                    animate: false,
                    highlight: true,
                    sortable: true,
                    respectParents: true
                });


                </script>
                {/literal}
            </td>
{else}
            <td>&nbsp;</td>
{/if}
            <td colspan="4">{$form.tag_search.label}  {help id="id-all-tags"}<br />{$form.tag_search.html|crmReplace:class:huge}</td>
        </tr>
        <tr>
            <td colspan="5">{include file="CRM/common/Tag.tpl"}</td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="form-layout-compressed">
                <tr>
                    <td colspan="2">
                        {$form.privacy_toggle.html} {help id="id-privacy"}
                    </td>
                </tr>
                <tr>
                    <td>
                        {$form.privacy_options.html}
                    </td>
                    <td>
                        {$form.privacy_operator.html}
                    </td>
                </tr>
                </table>
                {literal}
                  <script type="text/javascript">
                    cj("select#privacy_options").crmasmSelect({
                     addItemTarget: 'bottom',
                     animate: false,
                     highlight: true,
                     sortable: true,
                    });
                  </script>
                {/literal}
            </td>
            <td colspan="3">
                {$form.preferred_communication_method.label}<br />
                {$form.preferred_communication_method.html}<br />
                <div class="spacer"></div>
                {$form.email_on_hold.html} {$form.email_on_hold.label}
            </td>
        </tr>
        <tr>
            <td>
                {$form.contact_source.label}<br />
                {$form.contact_source.html|crmReplace:class:medium}
            </td>
            <td>
                {if $form.uf_user}
                    {$form.uf_user.label} {$form.uf_user.html} <span class="crm-clear-link">(<a href="#" title="unselect" onclick="unselectRadio('uf_user', 'Advanced'); return false;" >{ts}clear{/ts}</a>)</span>
                    <div class="description font-italic">
                        {ts 1=$config->userFramework}Does the contact have a %1 Account?{/ts}
                    </div>
                {else}
                    &nbsp;
                {/if}
            </td>
            <td>
                {$form.job_title.label}<br />
                {$form.job_title.html|crmReplace:class:medium}
            </td>
        </tr>
        <tr>
             <td>
                 {$form.id.label}<br />
                 {$form.id.html|crmReplace:class:medium}
             </td>
             <td>
                 {$form.external_identifier.label}<br />
                 {$form.external_identifier.html|crmReplace:class:medium}
             </td>
            <td colspan="3">
                {$form.preferred_language.label}<br />
                {$form.preferred_language.html|crmReplace:class:medium}
            </td>
        </tr>
    </table>
