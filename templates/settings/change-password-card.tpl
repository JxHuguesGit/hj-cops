    <div class="card bg-dark text-white" style="width: 18rem; margin: auto;">
        <div class="card-body">

            <form class="row g-3" method="post">
                <div class="col-12">
                    <label for="oldMdp" class="form-label">Ancien mot de passe</label>
                    <input type="password" class="form-control" id="oldMdp" name="oldMdp" required/>
                </div>
                <div class="col-12">
                    <label for="newMdp" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newMdp" name="newMdp" required/>
                </div>
                <div class="col-12">
                    <label for="confirmMdp" class="form-label">Confirmation</label>
                    <input type="password" class="form-control" id="confirmMdp" name="confirmMdp" required/>
                </div>
                <div class="col-md-6">
                    <a href="/" class="btn btn-secondary">Annuler</a>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <input type="hidden" name="formName" value="changeMdp"/>
                    <button type="submit" class="btn btn-secondary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
