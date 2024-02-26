<section id="carouselFichePerso" class="carousel slide pt-5">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselFichePerso" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Caractéristiques"></button>
        <button type="button" data-bs-target="#carouselFichePerso" data-bs-slide-to="1" aria-label="Compétences et Stages"></button>
        <button type="button" data-bs-target="#carouselFichePerso" data-bs-slide-to="2" aria-label="Equipement"></button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item">
            <section>
                <div class="card bg-dark text-white">%1$s</div>
            </section>
        </div>

        <div class="carousel-item active">
            <section>
                <div class="card bg-dark text-white">%2$s</div>
            </section>
        </div>

    <div class="carousel-item">
            <section class="pt-5">
                <div class="card bg-warning text-white" style="width: 18rem; margin: auto;">
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
            </section>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselFichePerso" data-bs-slide="prev">
    <span class="carousel-control-prev-icon bg-dark rounded" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselFichePerso" data-bs-slide="next">
    <span class="carousel-control-next-icon bg-dark rounded" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</section>
