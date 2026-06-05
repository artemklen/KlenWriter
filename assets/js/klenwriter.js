(function () {
	'use strict';

	var STORAGE_DARK = 'klenwriter_dark_mode';
	var STORAGE_DISTRACTION = 'klenwriter_distraction_mode';
	var COOKIE_DARK = 'klenwriter_dark_mode';
	var COOKIE_DISTRACTION = 'klenwriter_distraction_mode';
	var ACTIVE = '1';
	var INACTIVE = '0';

	/**
	 * Reads a localStorage value without breaking older or privacy-restricted browsers.
	 *
	 * @param {string} key Storage key.
	 * @returns {string|null} Stored value.
	 */
	function getStorage(key) {
		try {
			return window.localStorage ? window.localStorage.getItem(key) : null;
		} catch (error) {
			return null;
		}
	}

	/**
	 * Saves a localStorage value when available.
	 *
	 * @param {string} key Storage key.
	 * @param {string} value Stored value.
	 */
	function setStorage(key, value) {
		try {
			if (window.localStorage) {
				window.localStorage.setItem(key, value);
			}
		} catch (error) {}
	}

	/**
	 * Reads a cookie value.
	 *
	 * @param {string} name Cookie name.
	 * @returns {string|null} Cookie value.
	 */
	function getCookie(name) {
		var cookies = document.cookie ? document.cookie.split('; ') : [];
		var prefix = encodeURIComponent(name) + '=';
		var i;

		for (i = 0; i < cookies.length; i += 1) {
			if (cookies[i].indexOf(prefix) === 0) {
				return decodeURIComponent(cookies[i].substring(prefix.length));
			}
		}

		return null;
	}

	/**
	 * Writes a cookie for mode persistence.
	 *
	 * @param {string} name Cookie name.
	 * @param {string} value Cookie value.
	 */
	function setCookie(name, value) {
		var days = window.klenWriterSettings && window.klenWriterSettings.cookieDays ? parseInt(window.klenWriterSettings.cookieDays, 10) : 180;
		var date = new Date();

		date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
		document.cookie = encodeURIComponent(name) + '=' + encodeURIComponent(value) + '; expires=' + date.toUTCString() + '; path=/; SameSite=Lax';
	}

	/**
	 * Reads persisted mode with localStorage taking priority over cookies.
	 *
	 * @param {string} storageKey localStorage key.
	 * @param {string} cookieName Cookie name.
	 * @returns {boolean|null} Saved state or null when absent.
	 */
	function getPersistedMode(storageKey, cookieName) {
		var stored = getStorage(storageKey);

		if (stored === ACTIVE || stored === INACTIVE) {
			return stored === ACTIVE;
		}

		stored = getCookie(cookieName);
		if (stored === ACTIVE || stored === INACTIVE) {
			return stored === ACTIVE;
		}

		return null;
	}

	/**
	 * Persists a mode to localStorage and cookies.
	 *
	 * @param {string} storageKey localStorage key.
	 * @param {string} cookieName Cookie name.
	 * @param {boolean} enabled Current mode state.
	 */
	function persistMode(storageKey, cookieName, enabled) {
		var value = enabled ? ACTIVE : INACTIVE;

		setStorage(storageKey, value);
		setCookie(cookieName, value);
	}

	/**
	 * Adds or removes a class with a fallback for older browsers.
	 *
	 * @param {Element} element Target element.
	 * @param {string} className Class name.
	 * @param {boolean} enabled Whether the class should exist.
	 */
	function toggleClass(element, className, enabled) {
		if (!element) {
			return;
		}

		if (element.classList) {
			element.classList.toggle(className, enabled);
			return;
		}

		if (enabled && element.className.indexOf(className) === -1) {
			element.className += ' ' + className;
		} else if (!enabled) {
			element.className = element.className.replace(new RegExp('(^|\\s)' + className + '(\\s|$)', 'g'), ' ');
		}
	}

	/**
	 * Finds all elements matching a selector list.
	 *
	 * @param {string} selectors CSS selector list.
	 * @returns {Element[]} Matching elements.
	 */
	function findTargets(selectors) {
		try {
			return Array.prototype.slice.call(document.querySelectorAll(selectors));
		} catch (error) {
			return [];
		}
	}

	/**
	 * Applies hidden state to configured distraction-mode elements.
	 *
	 * @param {boolean} enabled Whether distraction mode is enabled.
	 */
	function updateHiddenTargets(enabled) {
		var selectors = window.klenWriterSettings && window.klenWriterSettings.hideSelectors ? window.klenWriterSettings.hideSelectors : '';
		var targets = selectors ? findTargets(selectors) : [];

		targets.forEach(function (target) {
			toggleClass(target, 'kw-hidden-by-distraction', enabled);
			if (enabled) {
				target.setAttribute('aria-hidden', 'true');
			} else {
				target.removeAttribute('aria-hidden');
			}
		});
	}

	/**
	 * Applies a dark background helper class to theme elements that stay white.
	 *
	 * @param {boolean} enabled Whether dark mode is enabled.
	 */
	function updateDarkBackgroundTargets(enabled) {
		var selectors = window.klenWriterSettings && window.klenWriterSettings.darkBackgroundSelectors ? window.klenWriterSettings.darkBackgroundSelectors : '';
		var targets = selectors ? findTargets(selectors) : [];

		targets.forEach(function (target) {
			toggleClass(target, 'kw-dark-background', enabled);
		});
	}

	/**
	 * Updates a toggle button pressed state.
	 *
	 * @param {string} mode Mode name.
	 * @param {boolean} enabled Current state.
	 */
	function updateButton(mode, enabled) {
		var button = document.querySelector('[data-kw-toggle="' + mode + '"]');

		if (button) {
			button.setAttribute('aria-pressed', enabled ? 'true' : 'false');
			toggleClass(button, 'is-active', enabled);
		}
	}

	/**
	 * Applies all visual states.
	 *
	 * @param {boolean} darkEnabled Dark mode state.
	 * @param {boolean} distractionEnabled Distraction mode state.
	 */
	function applyModes(darkEnabled, distractionEnabled) {
		toggleClass(document.body, 'kw-dark-mode', darkEnabled);
		toggleClass(document.documentElement, 'kw-dark-mode', darkEnabled);
		toggleClass(document.body, 'kw-distraction-mode', distractionEnabled);
		updateDarkBackgroundTargets(darkEnabled);
		updateHiddenTargets(distractionEnabled);
		updateButton('dark', darkEnabled);
		updateButton('distraction', distractionEnabled);
	}

	/**
	 * Initializes frontend controls once the DOM is ready.
	 */
	function init() {
		var defaultMode = window.klenWriterSettings && window.klenWriterSettings.defaultMode === 'dark';
		var darkEnabled = getPersistedMode(STORAGE_DARK, COOKIE_DARK);
		var distractionEnabled = getPersistedMode(STORAGE_DISTRACTION, COOKIE_DISTRACTION);
		var darkButton = document.querySelector('[data-kw-toggle="dark"]');
		var distractionButton = document.querySelector('[data-kw-toggle="distraction"]');
		var exitButton = document.querySelector('[data-kw-exit-distraction]');

		darkEnabled = darkEnabled === null ? defaultMode : darkEnabled;
		distractionEnabled = distractionEnabled === null ? false : distractionEnabled;
		applyModes(darkEnabled, distractionEnabled);

		if (darkButton) {
			darkButton.onclick = function () {
				darkEnabled = !darkEnabled;
				persistMode(STORAGE_DARK, COOKIE_DARK, darkEnabled);
				applyModes(darkEnabled, distractionEnabled);
			};
		}

		if (distractionButton) {
			distractionButton.onclick = function () {
				distractionEnabled = !distractionEnabled;
				persistMode(STORAGE_DISTRACTION, COOKIE_DISTRACTION, distractionEnabled);
				applyModes(darkEnabled, distractionEnabled);
			};
		}

		if (exitButton) {
			exitButton.onclick = function () {
				distractionEnabled = false;
				persistMode(STORAGE_DISTRACTION, COOKIE_DISTRACTION, distractionEnabled);
				applyModes(darkEnabled, distractionEnabled);
			};
		}

		document.onkeydown = function (event) {
			event = event || window.event;
			if ((event.key === 'Escape' || event.keyCode === 27) && distractionEnabled) {
				distractionEnabled = false;
				persistMode(STORAGE_DISTRACTION, COOKIE_DISTRACTION, distractionEnabled);
				applyModes(darkEnabled, distractionEnabled);
			}
		};
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
