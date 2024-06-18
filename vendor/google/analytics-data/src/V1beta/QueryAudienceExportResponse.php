<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/analytics/data/v1beta/analytics_data_api.proto

namespace Google\Analytics\Data\V1beta;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A list of users in an audience export.
 *
 * Generated from protobuf message <code>google.analytics.data.v1beta.QueryAudienceExportResponse</code>
 */
class QueryAudienceExportResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Configuration data about AudienceExport being queried. Returned to help
     * interpret the audience rows in this response. For example, the dimensions
     * in this AudienceExport correspond to the columns in the AudienceRows.
     *
     * Generated from protobuf field <code>optional .google.analytics.data.v1beta.AudienceExport audience_export = 1;</code>
     */
    private $audience_export = null;
    /**
     * Rows for each user in an audience export. The number of rows in this
     * response will be less than or equal to request's page size.
     *
     * Generated from protobuf field <code>repeated .google.analytics.data.v1beta.AudienceRow audience_rows = 2;</code>
     */
    private $audience_rows;
    /**
     * The total number of rows in the AudienceExport result. `rowCount` is
     * independent of the number of rows returned in the response, the `limit`
     * request parameter, and the `offset` request parameter. For example if a
     * query returns 175 rows and includes `limit` of 50 in the API request, the
     * response will contain `rowCount` of 175 but only 50 rows.
     * To learn more about this pagination parameter, see
     * [Pagination](https://developers.google.com/analytics/devguides/reporting/data/v1/basics#pagination).
     *
     * Generated from protobuf field <code>optional int32 row_count = 3;</code>
     */
    private $row_count = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Google\Analytics\Data\V1beta\AudienceExport $audience_export
     *           Configuration data about AudienceExport being queried. Returned to help
     *           interpret the audience rows in this response. For example, the dimensions
     *           in this AudienceExport correspond to the columns in the AudienceRows.
     *     @type array<\Google\Analytics\Data\V1beta\AudienceRow>|\Google\Protobuf\Internal\RepeatedField $audience_rows
     *           Rows for each user in an audience export. The number of rows in this
     *           response will be less than or equal to request's page size.
     *     @type int $row_count
     *           The total number of rows in the AudienceExport result. `rowCount` is
     *           independent of the number of rows returned in the response, the `limit`
     *           request parameter, and the `offset` request parameter. For example if a
     *           query returns 175 rows and includes `limit` of 50 in the API request, the
     *           response will contain `rowCount` of 175 but only 50 rows.
     *           To learn more about this pagination parameter, see
     *           [Pagination](https://developers.google.com/analytics/devguides/reporting/data/v1/basics#pagination).
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Analytics\Data\V1Beta\AnalyticsDataApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Configuration data about AudienceExport being queried. Returned to help
     * interpret the audience rows in this response. For example, the dimensions
     * in this AudienceExport correspond to the columns in the AudienceRows.
     *
     * Generated from protobuf field <code>optional .google.analytics.data.v1beta.AudienceExport audience_export = 1;</code>
     * @return \Google\Analytics\Data\V1beta\AudienceExport|null
     */
    public function getAudienceExport()
    {
        return $this->audience_export;
    }

    public function hasAudienceExport()
    {
        return isset($this->audience_export);
    }

    public function clearAudienceExport()
    {
        unset($this->audience_export);
    }

    /**
     * Configuration data about AudienceExport being queried. Returned to help
     * interpret the audience rows in this response. For example, the dimensions
     * in this AudienceExport correspond to the columns in the AudienceRows.
     *
     * Generated from protobuf field <code>optional .google.analytics.data.v1beta.AudienceExport audience_export = 1;</code>
     * @param \Google\Analytics\Data\V1beta\AudienceExport $var
     * @return $this
     */
    public function setAudienceExport($var)
    {
        GPBUtil::checkMessage($var, \Google\Analytics\Data\V1beta\AudienceExport::class);
        $this->audience_export = $var;

        return $this;
    }

    /**
     * Rows for each user in an audience export. The number of rows in this
     * response will be less than or equal to request's page size.
     *
     * Generated from protobuf field <code>repeated .google.analytics.data.v1beta.AudienceRow audience_rows = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getAudienceRows()
    {
        return $this->audience_rows;
    }

    /**
     * Rows for each user in an audience export. The number of rows in this
     * response will be less than or equal to request's page size.
     *
     * Generated from protobuf field <code>repeated .google.analytics.data.v1beta.AudienceRow audience_rows = 2;</code>
     * @param array<\Google\Analytics\Data\V1beta\AudienceRow>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setAudienceRows($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Analytics\Data\V1beta\AudienceRow::class);
        $this->audience_rows = $arr;

        return $this;
    }

    /**
     * The total number of rows in the AudienceExport result. `rowCount` is
     * independent of the number of rows returned in the response, the `limit`
     * request parameter, and the `offset` request parameter. For example if a
     * query returns 175 rows and includes `limit` of 50 in the API request, the
     * response will contain `rowCount` of 175 but only 50 rows.
     * To learn more about this pagination parameter, see
     * [Pagination](https://developers.google.com/analytics/devguides/reporting/data/v1/basics#pagination).
     *
     * Generated from protobuf field <code>optional int32 row_count = 3;</code>
     * @return int
     */
    public function getRowCount()
    {
        return isset($this->row_count) ? $this->row_count : 0;
    }

    public function hasRowCount()
    {
        return isset($this->row_count);
    }

    public function clearRowCount()
    {
        unset($this->row_count);
    }

    /**
     * The total number of rows in the AudienceExport result. `rowCount` is
     * independent of the number of rows returned in the response, the `limit`
     * request parameter, and the `offset` request parameter. For example if a
     * query returns 175 rows and includes `limit` of 50 in the API request, the
     * response will contain `rowCount` of 175 but only 50 rows.
     * To learn more about this pagination parameter, see
     * [Pagination](https://developers.google.com/analytics/devguides/reporting/data/v1/basics#pagination).
     *
     * Generated from protobuf field <code>optional int32 row_count = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setRowCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->row_count = $var;

        return $this;
    }

}

