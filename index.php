<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Metode Electre Hernanda-TI3E</title> 
    <?php include("koneksi.php"); ?>
    <?php error_reporting(0) ?>
</head>
    <body>
        <?php 
        //Pemanggilan fungsi
        require dirname(__DIR__) . '/electre/Atribut.php';
        require dirname(__DIR__) . '/electre/Concordance.php';
        require dirname(__DIR__) . '/electre/Discordance.php';
        require dirname(__DIR__) . '/electre/Bobot_View.php';
        $atribut = new Atribut();
        $discordance = new Discordance();
        $concordance = new Concordance();
        $bobot_view = new Bobot_View();

        $namaKriteria2=array();
        $arraySoal=array();
        $arrayConcordance=array();
        $arrayDisordance=array();
        $arrayMatriksDominanConcordance=array();
        $arrayMatriksDominanDisordance=array();
        $arrayAgregatDominanceMatriks=array();
        $statusInputan=false; ?>
        <table border="3">
            <center><h1>
            SISTEM PENDUKUNG KEPUTUSAN <br>
            Metode Electre
            </h1></center>
            <h3>Hernanda Candra P. {1741720184}<br>
                Politeknik Negeri Malang
            </h3>
            
            <tr>          
                <td>Alternatif</td>
                <td>Fasilitas</td>
                <td>Harga</td>
                <td>Tahun</td>
                <td>Jarak</td>
                <td>Keamanan</td>
            </tr>
            <?php $query="select * from alternatif";
            $hasil=mysqli_query($koneksi,$query);           
            $i=0;
            while($data=mysqli_fetch_array($hasil, MYSQLI_ASSOC)){
            ?>
            <tr>
                <td><?php echo $namaKriteria2[$i]=$data['alternatif'] ?></td>
                <td><?php echo $arraySoal[$i][0]=$data['fasilitas'] ?></td>
                <td><?php echo $arraySoal[$i][1]=$data['harga'] ?></td>
                <td><?php echo $arraySoal[$i][2]=$data['tahun'] ?></td>
                <td><?php echo $arraySoal[$i][3]=$data['jarak'] ?></td>
                <td><?php echo $arraySoal[$i][4]=$data['keamanan'] ?></td>
            </tr> 
            <?php $i++; } ?>
        </table>
        
        <?php 
        $namaKriteria=$namaKriteria2;
        $namaKriteria[count($namaKriteria)]="Bobot";
        $kriteria=array("Fasilitas","Harga","Tahun","Jarak","Keamanan");
        $banyakAlternatif=$i+1;
        
        echo "<br>"."<b>1. KRITERIA</b>"."<br>";
        $arraySoal= $bobot_view->Bobot($arraySoal);
        $bobot_view->printArray4($arraySoal);

        echo "<br>"."<b>2. BOBOT NORMALISASI</b>"."<br>";
        $arrayXDataNilai=$arraySoal;
        $arrayXDataNilai=$atribut->xDataNilai($arraySoal,$arrayXDataNilai);
        $bobot_view->printArray4($arrayXDataNilai);

        echo "<br>"."<b>3. HASIL NORMALISASI</b>"."<br>";
        $arrayRNormalisasi=$arraySoal;
        $arrayRNormalisasi=$atribut->rNormalisasi($arrayXDataNilai,$arrayRNormalisasi);
        $bobot_view->printArray4($arrayRNormalisasi);

        echo "<br>"."<b>4. MENGALIKAN BOBOT DENGAN KRITERIA</b>"."<br>";
        $arrayV=$arraySoal;
        $arrayV=$atribut->tabelV($arrayV,$arrayRNormalisasi);
        $bobot_view->printArray4($arrayV);

        echo "<br>"."<b>5.A. CONCORDANCE</b>"."<br>";
        $arrayConcordance=$concordance->tabelConcordance($arrayConcordance,$arrayV);            
        $bobot_view->printArray3x3($arrayConcordance);

        echo "<b>5.B. MATRIKS CONCORDANCE</b>"."<br>";
        $arrayMatriksConcordance=$arrayConcordance;
        $arrayMatriksConcordance=$concordance->matriksConcordance($arrayMatriksConcordance,$arrayV,$arraySoal);
        $bobot_view->printArray3x3($arrayMatriksConcordance);
        
        $tresholdConcordance=$concordance->tresholdConcordance($arrayMatriksConcordance);
        echo "<b>5.C. TRESHOLD CONCORDANCE</b>"."<br>".$tresholdConcordance."<br>";

        echo "<br><b>5.D. MATRIKS DOMINAN CONCORDANCE</b>"."<br>";
        $arrayMatriksDominanConcordance=$concordance->matriksDominanConcordance($arrayMatriksDominanConcordance,$tresholdConcordance,$arrayMatriksConcordance);
        $bobot_view->printArray3x3($arrayMatriksDominanConcordance);

        echo "<b>6.A. DISORDANCE</b>"."<br>";
        $arrayDisordance=$discordance->tabelDisordance($arrayDisordance,$arrayV);
        $bobot_view->printArray3x3($arrayDisordance);

        echo "<b>6.B. MATRIKS DISORDANCE</b>"."<br>";
        $arrayMatriksDisordance=$arrayDisordance;
        $arrayMatriksDisordance=$discordance->matriksDisordance($arrayMatriksDisordance,$arrayV);
        $bobot_view->printArray3x3($arrayMatriksDisordance);

        $tresholdDisordance=$discordance->tresholdDisordance($arrayMatriksDisordance);
        echo "<b>6.C. TRESHOLD DISORDANCE</b>"."<br>".$tresholdDisordance."<br>"."<br>";

        echo "<b>6.D.MATRIKS DOMINAN DISORDANCE</b>"."<br>";
        $arrayMatriksDominanDisordance=$discordance->matriksDominanDisordance($arrayMatriksDominanDisordance,$tresholdDisordance,$arrayMatriksDisordance);
        $bobot_view->printArray3x3($arrayMatriksDominanDisordance);

        echo "<b>7. MATRIKS AGREGASI</b>"."<br>";
        $arrayAgregatDominanceMatriks=$atribut->agregatDominanceMatriks($arrayAgregatDominanceMatriks,$arrayMatriksDominanConcordance,$arrayMatriksDominanDisordance);
        $bobot_view->printArray3x3($arrayAgregatDominanceMatriks);

        echo "<b>8. PILIHAN TERBAIK :</b>"."<br>";
        $jawaban=$atribut->perangkingan($arrayAgregatDominanceMatriks);
            if($jawaban==10){echo "Tidak ada alternatif yang sesuai";}else{
        echo "<b>Alternatif = </b>".$namaKriteria[$jawaban];  }
        ?>
    </body>
</html>