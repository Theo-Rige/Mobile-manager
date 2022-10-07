<?php
global $wpdb;

function removeslashes($string)
{
    $string = implode("", explode("\\", $string));
    return stripslashes(trim($string));
}

$positions = [
    'burger' => [
        'En haut à gauche' => 'top_left',
        'En haut à droite' => 'top_right',
        'En bas à gauche' => 'bottom_left',
        'En bas à droite' => 'bottom_right',
        'Shortcode' => 'shortcode'
    ],
    'search' => [
        'À droite' => 'top_right',
        'À gauche' => 'top_left',
    ]

];

$editor_options = [
    'textarea_rows' => 5,
    'tinymce' => ['content_css' => plugins_url("kc-mobile-manager/style.css")]
];

if (!empty($_POST)) {
    // var_dump($_POST);
    $table = $wpdb->prefix . 'mobile_manager';
    $data = [];

    $selector = (!isset($_POST['selector'])) ? '' : $_POST['selector'];
    $selector_type = (!isset($_POST['selector-type'])) ? 'id' : $_POST['selector-type'];
    $logo = (!isset($_POST['logo'])) ? '' : $_POST['logo'];
    $logo_position = (!isset($_POST['logo_position'])) ? '' : $_POST['logo_position'];
    $back_color = (!isset($_POST['back_color'])) ? '' : $_POST['back_color'];
    $burger_color = (!isset($_POST['burger_color'])) ? '' : $_POST['burger_color'];
    $search = (!isset($_POST['search'])) ? '0' : $_POST['search'];
    $search_custom_icon = (!isset($_POST['search_custom_icon'])) ? '' : $_POST['search_custom_icon'];
    $search_color = (!isset($_POST['search_color'])) ? '' : $_POST['search_color'];
    $back_img = (!isset($_POST['back_img'])) ? '' : $_POST['back_img'];
    $content_before = (!isset($_POST['content_before'])) ? '' : $_POST['content_before'];
    $content_after = (!isset($_POST['content_after'])) ? '' : $_POST['content_after'];

    $data = [
        'active' => 1,
        'selector' => $selector,
        'selector_type' => $selector_type,
        'breakpoint' => $_POST['breakpoint'],
        'burger_position' => $_POST['burger_position'],
        'burger_color' => $burger_color,
        'search' => $search,
        'search_icon' => '1',
        'search_custom_icon' => $search_custom_icon,
        'search_color' => $search_color,
        'search_position' => $_POST['search_position'],
        'logo' => $logo,
        'logo_position' => $logo_position,
        'back_color' => $back_color,
        'back_opacity' => $_POST['back_opacity'],
        'back_img' => $back_img,
        'content_before' => $content_before,
        'content_after' => $content_after
    ];

    $where = ['id' => 1];
    $update = $wpdb->update($table, $data, $where);
    // exit( var_dump( $wpdb->last_query ) );
}

$params = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . 'mobile_manager' . " WHERE id = 1", ARRAY_A);

$insert_selector_type = ($params['selector_type'] == 'class') ? 'checked' : '';
$insert_search_state = ($params['search'] === '1') ? 'checked' : '';

// var_dump($params);

?>

<main class="mobile-manager mt-5">
    <div class="mt-10 sm:mt-0">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form id="settings-form" method="POST">
                    <div class="shadow overflow-hidden sm:rounded-md">
                        <div class="px-4 py-3 bg-gray-50 flex justify-between items-center sm:px-6">
                            <div class="grid grid-cols-2 divide-x <?= isset($update) && !$update ? 'divide-red-500' : 'divide-green-500' ?> items-center">
                                <h1 class="text-lg font-medium leading-6 text-gray-900 mr-4">Mobile Menu Manager <span class="text-sm text-gray-500">v2.1.0</span></h1>
                                <?php if (!empty($_POST)) : ?><span class="pl-4 <?= isset($update) && !$update ? 'text-red-500' : 'text-green-500' ?>"><?= isset($update) && !$update ? 'Une erreur est survenue !' : 'Les modifications ont été enregistrés' ?></span><?php endif; ?>
                            </div>
                            <button type="submit" class="inline-flex justify-center py-1 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-500" disabled>Sauvegarder</button>
                        </div>
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <div class="grid grid-cols-6 gap-8">

                                <div class="col-span-6">
                                    <h2 class=" font-medium text-gray-900 border-b border-gray-300 pb-2 text-lg mb-4">Selecteur du container du menu desktop</h2>
                                    <label class="text-sm font-medium text-gray-700 flex items-center">ID
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="selector-type" id="selector-type" value="class" <?= $insert_selector_type ?>>
                                            <div class="switch"></div>
                                        </label>CLASS
                                    </label>
                                    <input type="text" name="selector" id="selector" spellcheck="false" value="<?= $params["selector"] ?>" required class="col-span-5 mt-2 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">
                                </div>


                                <div class="col-span-6 flex flex-col">
                                    <h2 class="col-span-6 font-medium text-gray-900 border-b border-gray-300 pb-2 text-lg mb-4">Header</h2>

                                    <h3 class="col-span-6 font-medium text-gray-700 border-b border-gray-200 pb-2 text-base mb-4">Burger</h3>
                                    <div class="col-span-6 grid grid-cols-6 gap-6 mb-8">

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="burger_color" class="block text-sm font-medium text-gray-500">Couleur</label>
                                            <div class="mt-1 flex rounded-md">
                                                <input type="text" name="burger_color" id="burger_color" spellcheck="false" placeholder="#FFFFFF" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="<?= $params['burger_color'] ?>" class=" focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">
                                                <span id="burger_color_preview" class="w-60 inline-flex items-center px-3 rounded-r-md shadow-sm bg-gray-50 text-gray-500 text-sm"></span>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="company-website" class="block text-sm font-medium text-gray-500">Breakpoint</label>
                                            <div class="mt-1 flex rounded-md">
                                                <input type="text" name="breakpoint" pattern="\d*" id="breakpoint" value="<?= $params['breakpoint'] ?>" placeholder="1024" required class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full sm:text-sm ">
                                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 shadow-sm bg-gray-50 text-gray-500 text-sm">px</span>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="burger_position" class="block text-sm font-medium text-gray-500">Position</label>
                                            <select id="burger_position" name="burger_position" class="mt-1 block w-full py-2 px-3 border  bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <?php
                                                foreach ($positions['burger'] as $key => $position) :
                                                    if ($position == $params['burger_position']) : ?>
                                                        <option value="<?= $position ?>" selected><?= $key ?></option>
                                                    <?php else : ?>
                                                        <option value="<?= $position ?>"><?= $key ?></option>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-500">Shortcode</label>
                                            <div class="mt-1 w-full inline-flex items-center px-3 py-[6px] rounded border bg-gray-50 text-gray-500 text-sm">[kc_mobile_manager]</div>
                                        </div>
                                    </div>

                                    <h3 class="col-span-6 flex justify-between items-center font-medium text-gray-700 border-b border-gray-200 pb-2 text-base mb-4"><span>Recherche</span>
                                        <label class="text-sm font-medium text-gray-700 flex items-center">Activer la recherche ?
                                            <label class="toggle-switch --binary">
                                                <input type="checkbox" name="search" id="search" value="1" <?= $insert_search_state ?>>
                                                <div class="switch"></div>
                                            </label>
                                        </label>
                                    </h3>
                                    <div class="col-span-6 grid grid-cols-6 gap-6">

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="search_icon" class="block text-sm font-medium text-gray-500">Icon</label>
                                            <div class="mt-1 flex rounded-md">
                                                <span class="text-sm font-medium text-gray-300">Comming soon</span>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="search_custom_icon" class="block text-sm font-medium text-gray-500">Icon SVG personnalisé</label>
                                            <div class="mt-1 flex rounded-md">
                                                <textarea id="search_custom_icon" name="search_custom_icon"><?= esc_textarea(removeslashes($params['search_custom_icon'])) ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="search_color" class="block text-sm font-medium text-gray-500">Couleur</label>
                                            <div class="mt-1 flex rounded-md">
                                                <input type="text" name="search_color" id="search_color" spellcheck="false" placeholder="#FFFFFF" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="<?= $params['search_color'] ?>" class=" focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">
                                                <span id="search_color_preview" class="w-60 inline-flex items-center px-3 rounded-r-md shadow-sm bg-gray-50 text-gray-500 text-sm"></span>
                                            </div>
                                        </div>



                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="search_position" class="block text-sm font-medium text-gray-500">Position</label>
                                            <select id="search_position" name="search_position" class="mt-1 block w-full py-2 px-3 border  bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <?php
                                                foreach ($positions['search'] as $key => $position) :
                                                    if ($position == $params['search_position']) : ?>
                                                        <option value="<?= $position ?>" selected><?= $key ?></option>
                                                    <?php else : ?>
                                                        <option value="<?= $position ?>"><?= $key ?></option>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-span-6 grid grid-cols-2">
                                    <h2 class="col-span-2 font-medium text-gray-900 border-b border-gray-300 pb-2 text-lg mb-4">Logo</h2>
                                    <div class="pr-3 border-r border-gray-300">
                                        <label for="logo" class="block text-sm font-medium text-gray-500">Url</label>
                                        <input type="url" name="logo" id="logo" value="<?= $params['logo'] ?>" placeholder="http://www.exemple.com" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">
                                        <fieldset class="mt-6">
                                            <legend class="block text-sm font-medium text-gray-500">Logo position</legend>
                                            <div class="mt-4 space-y-4">
                                                <div class="flex items-center">
                                                    <input type="radio" id="img_left" name="logo_position" value="flex-start" <?php if ($params['logo_position'] == 'flex-start')  echo 'checked'; ?>>
                                                    <label for="img_left" class="ml-3 block text-sm font-medium text-gray-700">Gauche</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="img_center" name="logo_position" value="center" <?php if ($params['logo_position'] == 'center') echo 'checked'; ?>>
                                                    <label for="img_center" class="ml-3 block text-sm font-medium text-gray-700">Centrer</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="img_right" name="logo_position" value="flex-end" <?php if ($params['logo_position'] == 'flex-end') echo 'checked';                                                                                                                                    ?>>
                                                    <label for="img_right" class="ml-3 block text-sm font-medium text-gray-700">Droite</label>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="ml-3 flex items-center justify-center">
                                        <?php if ($params['logo']) : ?>
                                            <img id="logo_preview" src="<?= $params['logo'] ?>" alt="logo" class="max-h-52">
                                        <?php else : ?>
                                            <span class="text-sm font-medium text-gray-300">Aucun logo n'a été enregistré</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-span-6 grid grid-cols-2">
                                    <h2 class="col-span-2 font-medium text-gray-900 border-b border-gray-300 pb-2 text-lg mb-4">Background</h2>
                                    <div class="pr-3 border-r border-gray-300">
                                        <label for="burger_color" class="block text-sm font-medium text-gray-500">Couleur</label>
                                        <input type="text" name="back_color" id="back_color" placeholder="#FFFFFF" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="<?= $params['back_color'] ?>" spellcheck="false" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">
                                        <label for="burger_color" class="mt-4 block text-sm font-medium text-gray-700">Opacité</label>
                                        <div class="flex items-center gap-2">
                                            <input type="range" min="0" max="1" step="0.1" name="back_opacity" id="back_opacity" value="<?= $params['back_opacity'] ?>" class="h-1 bg-gray-300 rounded-lg accent-blue-500 appearance-none cursor-pointer">
                                            <span id="back_opacity_text" class="font-medium text-gray-500"></span>
                                        </div>
                                        <label for="logo" class="mt-4 block text-sm font-medium text-gray-700">Image</label>
                                        <input type="url" name="back_img" id="back_img" value="<?= $params['back_img'] ?>" placeholder="http://www.exemple.com" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm ">

                                    </div>
                                    <div class="relative ml-3 flex items-center justify-center overflow-hidden">
                                        <span id="back_img_placeholder" class="text-sm font-medium text-gray-300">Aucune image n'a été enregistrée</span>
                                        <img id="back_img_preview" src="<?= $params['back_img'] ?>" alt="background" class="absolute object-cover">
                                        <div id="back_preview" class="absolute inset-0 z-10"></div>
                                    </div>
                                </div>

                                <div class="col-span-6 grid grid-cols-2">
                                    <h2 class="col-span-2 font-medium text-gray-900 border-b border-gray-300 pb-2 text-lg mb-4">Contenu</h2>
                                    <div class="pr-3 border-r border-gray-300">
                                        <label class="block text-sm font-medium text-gray-500">Avant</label>
                                        <?php wp_editor(stripslashes($params['content_before']), 'content_before', $editor_options); ?>

                                    </div>
                                    <div class="ml-3">
                                        <label class="block text-sm font-medium text-gray-500">Après</label>
                                        <?php wp_editor(stripslashes($params['content_after']), 'content_after', $editor_options); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" class="inline-flex justify-center py-1 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-500" disabled>Sauvegarder</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>