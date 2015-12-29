
plugin.tx_formule_pi1 {
	view {
		# cat=plugin.tx_formule_pi1/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:formule/Resources/Private/Templates/
		# cat=plugin.tx_formule_pi1/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:formule/Resources/Private/Partials/
		# cat=plugin.tx_formule_pi1/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:formule/Resources/Private/Layouts/
	}
	persistence {
		# cat=plugin.tx_formule_pi1//a; type=string; label=Default storage PID
		storagePid =
	}
}
