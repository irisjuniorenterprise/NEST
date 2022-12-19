/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css'

import feather from "feather-icons"
feather.replace({
    width: 14,
    height: 14
})
import './js/core/app-menu'
import './js/core/app'

// start the Stimulus application
import './bootstrap'
