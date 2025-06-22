<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Section extends Controller
{
    public function create()
    {
        try {
            $json = $this->request->getJSON(true);

            if (!$json || empty($json['survey_id']) || empty($json['name'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields.'
                ]);
            }

            $surveyId = (int)$json['survey_id'];
            $name     = $json['name'];
            $desc     = $json['description'] ?? '';
            $order    = 1; // You can add logic to auto-increment ORDER_BY later

            // Connect using your username & password pita207 / pita207
            $conn = oci_connect('pita207', 'pita207', 'localhost/XEPDB1', 'AL32UTF8');
            if (!$conn) {
                $err = oci_error();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ Oracle DB connection failed',
                    'error'   => $err['message']
                ]);
            }

                        // 1) figure out WHERE this new section should live
            $seqStmt = oci_parse($conn, "
            SELECT NVL(MAX(ORDER_BY),0) + 1 AS NEXT_ORDER
                FROM SECTIONS
            WHERE SURVEY_ID = :survey_id
            ");
            oci_bind_by_name($seqStmt, ':survey_id', $surveyId);
            oci_execute($seqStmt);
            $row   = oci_fetch_assoc($seqStmt);
            $order = (int) $row['NEXT_ORDER'];
            oci_free_statement($seqStmt);

            $sql = " INSERT INTO SECTIONS (
                            ID, SURVEY_ID, NAME, DESCRIPTION, ORDER_BY
                        ) VALUES (
                            SECTION_SEQ.NEXTVAL,
                            :survey_id,
                            :name,
                            EMPTY_CLOB(),
                            :order_by
                        )
                        RETURNING DESCRIPTION INTO :desc_clob
                        ";

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':survey_id', $surveyId);
            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':order_by', $order);

            $descClob = oci_new_descriptor($conn, OCI_D_LOB);
            oci_bind_by_name($stmt, ':desc_clob', $descClob, -1, OCI_B_CLOB);

            if (oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
                if ($descClob->save($desc)) {
                    oci_commit($conn);
                    $descClob->free();
                    oci_free_statement($stmt);
                    oci_close($conn);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => '✅ Section saved successfully!'
                    ]);
                } else {
                    oci_rollback($conn);
                    $descClob->free();
                    oci_free_statement($stmt);
                    oci_close($conn);

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => '❌ Failed to write section description.'
                    ]);
                }
            } else {
                $e = oci_error($stmt);
                oci_rollback($conn);
                $descClob->free();
                oci_free_statement($stmt);
                oci_close($conn);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ Failed to insert section.',
                    'error'   => $e['message']
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Exception thrown',
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function delete($id = null)
{
    try {
        // 1) Basic validation
        if (! $id || ! is_numeric($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid section ID.'
            ]);
        }

        // 2) Connect to Oracle
        $conn = oci_connect(
            'pita207',
            'pita207',
            '//127.0.0.1:49161/xepdb1',
            'AL32UTF8'
        );
        if (! $conn) {
            $e = oci_error();
            throw new \RuntimeException($e['message']);
        }

        // 3) Delete all questions in this section
        $sqlQ  = 'DELETE FROM QUESTIONS WHERE SECTION_ID = :id';
        $stidQ = oci_parse($conn, $sqlQ);
        oci_bind_by_name($stidQ, ':id', $id);
        if (! oci_execute($stidQ, OCI_NO_AUTO_COMMIT)) {
            $e = oci_error($stidQ);
            throw new \RuntimeException('Failed deleting questions: '.$e['message']);
        }
        oci_free_statement($stidQ);

        // 4) Delete the section row
        $sqlS  = 'DELETE FROM SECTIONS WHERE ID = :id';
        $stidS = oci_parse($conn, $sqlS);
        oci_bind_by_name($stidS, ':id', $id);
        if (! oci_execute($stidS, OCI_NO_AUTO_COMMIT)) {
            $e = oci_error($stidS);
            throw new \RuntimeException('Failed deleting section: '.$e['message']);
        }
        oci_free_statement($stidS);

        // 5) Commit both deletes together
        oci_commit($conn);
        oci_close($conn);

        // 6) Return JSON success
        return $this->response->setJSON([
            'success' => true,
            'message' => '✅ Section and its questions deleted.'
        ]);

    } catch (\Throwable $e) {
        // Rollback if something went wrong
        if (isset($conn)) {
            @oci_rollback($conn);
            @oci_close($conn);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => '❌ Delete failed: ' . $e->getMessage()
        ]);
    }
}

   public function update($id = null)
    {
        // Grab JSON body
        $json = $this->request->getJSON(true);

        // Basic validation
        if (! $id || ! is_numeric($id) || empty($json['name'])) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['success'=>false,'message'=>'Missing section name or invalid ID']);
        }

        $model = new SectionModel();

        // Update the record
        $ok = $model->update((int)$id, [
            'NAME'        => $json['name'],
            'DESCRIPTION' => $json['description'] ?? ''
        ]);

        // Return JSON for your JS
        return $this->response->setJSON([
            'success' => (bool)$ok,
            'message' => $ok 
                ? '✅ Section updated successfully.' 
                : '❌ Section update failed.'
        ]);
    }

}
