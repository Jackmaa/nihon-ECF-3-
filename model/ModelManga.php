<?php
class ModelManga extends Model {
    public function getMangaListByCat() {
        $req    = $this->getDb()->query('SELECT * FROM category, manga WHERE category.name = "shonen" AND manga.thumbnail != ""');
        $data   = $req->fetchAll(PDO::FETCH_ASSOC);
        $mangas = [];
        foreach ($data as $manga) {
            $mangas[] = new Manga($manga);
        }
        return $mangas;
    }

    public function getManga(int $id){
        $req = $this->getDb()->prepare('SELECT `id_manga`, `name`, `id_author`, `description`, `published_date`, `thumbnail` FROM `manga` WHERE id_manga = :id');
        $req->bindParam('id', $id, PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);

        return $result ? new Manga($result):null;
    }

    public function updateManga(int $id, string $name, string $description, string $published_date, string $thumbnail){

        $req = $this->getDb()->prepare('UPDATE `manga` SET `name` = :name, `description` = :description, `published_date` = :published_date, `thumbnail` = :thumbnail WHERE id_manga = :id');
        $req->bindParam(':name', $name, PDO::PARAM_STR);
        $req->bindParam(':description', $description, PDO::PARAM_STR);
        $req->bindParam(':published_date', $published_date, PDO::PARAM_STR);
        $req->bindParam(':thumbnail', $thumbnail, PDO::PARAM_STR);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        $req->execute();
    }
}

/*foreach ($mangas as $manga):
    <div class="manga">
        <a href="#"><img src="<?php echo $manga->getThumbnail() ?>" alt="<?php echo $manga->getName() ?>"><?php echo $manga->getName() ?></a>
        <span class="heart"><img src="public\asset\img\heart.svg" alt="Heart"></span>
        <figure><img src="public\asset\img\star.svg" alt="">(300)</figure>
    </div>
endforeach; ?>*/