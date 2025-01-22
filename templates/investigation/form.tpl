        <div class="input-group mb-3">
            <span for="nomEnquete" class="input-group-text col-2">Nom de l'enquête</span>
            <input id="nomEnquete" name="nomEnquete" type="text" class="form-control col-10" aria-label="Nom de l'enquête" aria-describedby="Nom de l'enquête" value="%2$s" %1$s>
        </div>
        <div class="input-group mb-3">
            <span for="enqueteurId" class="input-group-text col-2">Premier enquêteur</span>
            <input id="enqueteurId" name="enqueteurId" type="text" class="form-control col-4" aria-label="Premier enquêteur" aria-label="Premier enquêteur" value="%4$s" %3$s/>
            <span for="districtAttorneyId" class="input-group-text col-2">District Attorney</span>
            <input id="districtAttorneyId" name="districtAttorneyId" type="text" class="form-control col-4" aria-label="District Attorney" aria-label="District Attorney" value="%5$s" %3$s/>
        </div>
        <div class="input-group mb-3">
            <span for="facts" class="input-group-text col-2">Résumé des faits</span>
            <textarea id="facts" name="facts" class="form-control col-10 text-xs" aria-label="Résumé des faits" aria-describedby="Résumé des faits" %3$s>%6$s</textarea>
        </div>
        <hr/>
        <h5>Scène de crime</h5>
        <div class="input-group mb-3">
            <span for="sdcDescription" class="input-group-text col-2">Description</span>
            <textarea id="sdcDescription" name="sdcDescription" class="form-control col-10 text-xs" aria-label="Description" aria-describedby="Description" %3$s>%7$s</textarea>
        </div>
        <div class="input-group mb-3">
            <span for="sdcClues" class="input-group-text col-2">Indices relevés</span>
            <textarea id="sdcClues" name="sdcClues" class="form-control col-10 text-xs" aria-label="Indices relevés" aria-describedby="Indices relevés" %3$s>%7$s</textarea>
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text col-2">Autopsie</span>
            <span class="input-group-text col-10">%8$s</span>
        </div>
        <hr/>
        <h5>Pistes / Démarches</h5>
        <div class="input-group mb-3">
            <textarea id="pistesDemarches" name="pistesDemarches" class="form-control col-12 text-xs" aria-label="Pistes / Démarches" aria-describedby="Pistes / Démarches" %3$s>%9$s</textarea>
        </div>
