<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Delivery Terms
 *
 * An identifier that determines the point in which the title transfer and risk of a supply takes place. A Delivery Term
 * Code identifies the importer of record on cross-border transactions. These terms are also known as Shipping Terms or
 * Incoterm. Delivery Term information is critical in Value Added Tax transactions to determine the place of supply in
 * distance selling. For detailed explanations of these terms, refer to the Terms of Sales definition in the glossary of
 * the U.S. Department of Transportation Maritime Administration Web site at
 * https://www.marad.dot.gov/wp-content/uploads/pdf/Glossary_final.pdf
 *
 * @api
 */
interface DeliveryTerm
{
    /**
     * Cost and Freight
     *
     * Customer is the importer of record
     */
    const CFR = 'CFR';

    /**
     * Cost Insurance and Freight
     *
     * Customer is the imported of record
     */
    const CIF = 'CIF';

    /**
     * Carriage Insurance Paid To
     *
     *The customer is the importer of record
     */
    const CIP = 'CIP';

    /**
     * Carriage Paid To
     *
     * The customer is the importer of record
     */
    const CPT = 'CPT';

    /**
     * Customer Ships
     *
     * The customer is the importer of record
     */
    const CUS = 'CUS';

    /**
     * Delivered at Frontier
     *
     * The customer is the importer of record
     */
    const DAF = 'DAF';

    /**
     * Delivered at Place
     *
     * The customer is the importer of record
     */
    const DAP = 'DAP';

    /**
     * Delivered at Terminal
     *
     * The customer is the importer of record
     */
    const DAT = 'DAT';

    /**
     * Delivery Duty Paid
     *
     * The supplier is the importer of record
     */
    const DDP = 'DDP';

    /**
     * Delivery Duty Unpaid
     *
     * The customer is the importer of record
     */
    const DDU = 'DDU';

    /**
     * Delivered Ex Quay Duty Unpaid
     *
     * The customer is the importer of record
     */
    const DEQ = 'DEQ';

    /**
     * Delivered Ex-Ship
     *
     * The customer is the importer of record
     */
    const DES = 'DES';

    /**
     * Ex Works
     *
     * The customer is the importer of record
     */
    const EXW = 'EXW';

    /**
     * Free Along Side Ship
     *
     * The customer is the importer of record
     */
    const FAS = 'FAS';

    /**
     * Free Carrier
     *
     * The customer is the importer of record
     */
    const FCA = 'FCA';

    /**
     * Free Onboard Vessel
     *
     * The customer is the importer of record
     */
    const FOB = 'FOB';

    /**
     * Supplier Ships
     *
     * The supplier is the importer of record
     */
    const SUP = 'SUP';
}
