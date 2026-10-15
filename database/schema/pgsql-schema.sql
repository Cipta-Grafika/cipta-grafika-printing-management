--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

-- *not* creating schema, since initdb creates it


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS '';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: m_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_categories_id_seq OWNED BY public.m_categories.id;


--
-- Name: m_digital_lamination_pricing_rule_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_lamination_pricing_rule_details (
    id bigint NOT NULL,
    digital_lamination_pricing_rule_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    print_type character varying(255) NOT NULL,
    quantity_tier character varying(255) NOT NULL,
    minimum_quantity integer NOT NULL,
    maximum_quantity integer,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_lamination_pricing_rule_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_lamination_pricing_rule_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_lamination_pricing_rule_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_lamination_pricing_rule_details_id_seq OWNED BY public.m_digital_lamination_pricing_rule_details.id;


--
-- Name: m_digital_lamination_pricing_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_lamination_pricing_rules (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL,
    vendor_id bigint,
    category_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_lamination_pricing_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_lamination_pricing_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_lamination_pricing_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_lamination_pricing_rules_id_seq OWNED BY public.m_digital_lamination_pricing_rules.id;


--
-- Name: m_digital_lamination_production_cost_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_lamination_production_cost_details (
    id bigint NOT NULL,
    digital_lamination_production_cost_id bigint NOT NULL,
    lamination_id bigint NOT NULL,
    category_id bigint NOT NULL,
    paper_cost numeric(12,2) NOT NULL,
    click_cost numeric(12,2) NOT NULL,
    single_print_cost numeric(12,2) NOT NULL,
    double_print_cost numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_lamination_production_cost_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_lamination_production_cost_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_lamination_production_cost_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_lamination_production_cost_details_id_seq OWNED BY public.m_digital_lamination_production_cost_details.id;


--
-- Name: m_digital_lamination_production_cost_prices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_lamination_production_cost_prices (
    id bigint NOT NULL,
    digital_lamination_production_cost_detail_id bigint NOT NULL,
    digital_lamination_pricing_rule_detail_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    print_type character varying(255) NOT NULL,
    quantity_tier character varying(255) NOT NULL,
    minimum_quantity integer NOT NULL,
    maximum_quantity integer,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    selling_price numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_lamination_production_cost_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_lamination_production_cost_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_lamination_production_cost_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_lamination_production_cost_prices_id_seq OWNED BY public.m_digital_lamination_production_cost_prices.id;


--
-- Name: m_digital_lamination_production_costs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_lamination_production_costs (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL,
    vendor_id bigint,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_lamination_production_costs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_lamination_production_costs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_lamination_production_costs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_lamination_production_costs_id_seq OWNED BY public.m_digital_lamination_production_costs.id;


--
-- Name: m_digital_pricing_rule_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_pricing_rule_details (
    id bigint NOT NULL,
    digital_pricing_rule_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    print_type character varying(255) NOT NULL,
    quantity_tier character varying(255) NOT NULL,
    minimum_quantity integer NOT NULL,
    maximum_quantity integer,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_pricing_rule_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_pricing_rule_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_pricing_rule_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_pricing_rule_details_id_seq OWNED BY public.m_digital_pricing_rule_details.id;


--
-- Name: m_digital_pricing_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_pricing_rules (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL,
    vendor_id bigint,
    category_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_pricing_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_pricing_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_pricing_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_pricing_rules_id_seq OWNED BY public.m_digital_pricing_rules.id;


--
-- Name: m_digital_production_cost_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_production_cost_details (
    id bigint NOT NULL,
    digital_production_cost_id bigint NOT NULL,
    material_id bigint NOT NULL,
    paper_cost numeric(12,2) NOT NULL,
    click_cost numeric(12,2) NOT NULL,
    single_print_cost numeric(12,2) NOT NULL,
    double_print_cost numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_production_cost_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_production_cost_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_production_cost_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_production_cost_details_id_seq OWNED BY public.m_digital_production_cost_details.id;


--
-- Name: m_digital_production_cost_prices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_production_cost_prices (
    id bigint NOT NULL,
    digital_production_cost_detail_id bigint NOT NULL,
    digital_pricing_rule_detail_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    print_type character varying(255) NOT NULL,
    quantity_tier character varying(255) NOT NULL,
    minimum_quantity integer NOT NULL,
    maximum_quantity integer,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    selling_price numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_production_cost_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_production_cost_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_production_cost_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_production_cost_prices_id_seq OWNED BY public.m_digital_production_cost_prices.id;


--
-- Name: m_digital_production_costs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_digital_production_costs (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL,
    vendor_id bigint,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_digital_production_costs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_digital_production_costs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_digital_production_costs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_digital_production_costs_id_seq OWNED BY public.m_digital_production_costs.id;


--
-- Name: m_discount_scopes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_discount_scopes (
    id bigint NOT NULL,
    discount_id bigint NOT NULL,
    engine_id bigint NOT NULL,
    category_id bigint NOT NULL
);


--
-- Name: m_discount_scopes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_discount_scopes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_discount_scopes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_discount_scopes_id_seq OWNED BY public.m_discount_scopes.id;


--
-- Name: m_discounts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_discounts (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    discount_percentage numeric(5,2) NOT NULL,
    starts_at timestamp(0) without time zone NOT NULL,
    ends_at timestamp(0) without time zone NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    code character varying(30) NOT NULL
);


--
-- Name: m_discounts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_discounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_discounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_discounts_id_seq OWNED BY public.m_discounts.id;


--
-- Name: m_display_pricing_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_pricing_rules (
    id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    markup_percentage numeric(5,2) NOT NULL,
    rounding_value numeric(12,2),
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL,
    category_id bigint NOT NULL
);


--
-- Name: m_display_pricing_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_pricing_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_pricing_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_pricing_rules_id_seq OWNED BY public.m_display_pricing_rules.id;


--
-- Name: m_display_product_component_prices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_product_component_prices (
    id bigint NOT NULL,
    configuration_id bigint NOT NULL,
    component_id bigint NOT NULL,
    general_price numeric(12,2) NOT NULL,
    division_price numeric(12,2) NOT NULL,
    plain_price numeric(12,2),
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    rounding_value_component numeric(15,2)
);


--
-- Name: m_display_product_component_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_product_component_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_product_component_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_product_component_prices_id_seq OWNED BY public.m_display_product_component_prices.id;


--
-- Name: m_display_product_components; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_product_components (
    id bigint NOT NULL,
    display_product_id bigint NOT NULL,
    component_type character varying(255) NOT NULL,
    material_id bigint,
    lamination_id bigint,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    CONSTRAINT m_display_product_components_component_type_check CHECK (((component_type)::text = ANY (ARRAY[('material'::character varying)::text, ('lamination'::character varying)::text])))
);


--
-- Name: m_display_product_components_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_product_components_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_product_components_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_product_components_id_seq OWNED BY public.m_display_product_components.id;


--
-- Name: m_display_product_configurations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_product_configurations (
    id bigint NOT NULL,
    cost_rangka numeric(12,2) NOT NULL,
    cost_finishing numeric(12,2) NOT NULL,
    total_cost numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    display_product_id bigint NOT NULL,
    engine_id bigint NOT NULL,
    location_id bigint NOT NULL
);


--
-- Name: m_display_product_configurations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_product_configurations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_product_configurations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_product_configurations_id_seq OWNED BY public.m_display_product_configurations.id;


--
-- Name: m_display_product_images; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_product_images (
    id bigint NOT NULL,
    display_product_id bigint NOT NULL,
    image_path text NOT NULL,
    image_name text NOT NULL,
    is_primary boolean NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_display_product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_product_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_product_images_id_seq OWNED BY public.m_display_product_images.id;


--
-- Name: m_display_product_prices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_product_prices (
    id bigint NOT NULL,
    display_product_id bigint NOT NULL,
    general_price numeric(12,2) NOT NULL,
    division_price numeric(12,2) NOT NULL,
    plain_price numeric(12,2),
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_display_product_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_product_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_product_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_product_prices_id_seq OWNED BY public.m_display_product_prices.id;


--
-- Name: m_display_products; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_display_products (
    id bigint NOT NULL,
    display_name character varying(255) NOT NULL,
    length numeric(12,2) NOT NULL,
    width numeric(12,2) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    category_id bigint NOT NULL
);


--
-- Name: m_display_products_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_display_products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_display_products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_display_products_id_seq OWNED BY public.m_display_products.id;


--
-- Name: m_engines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_engines (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    minimum_charge numeric(12,2)
);


--
-- Name: m_engines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_engines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_engines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_engines_id_seq OWNED BY public.m_engines.id;


--
-- Name: m_lamination_pricing_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_lamination_pricing_rules (
    id bigint NOT NULL,
    location_id bigint NOT NULL,
    category_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    engine_id bigint NOT NULL,
    vendor_id bigint
);


--
-- Name: m_lamination_pricing_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_lamination_pricing_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_lamination_pricing_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_lamination_pricing_rules_id_seq OWNED BY public.m_lamination_pricing_rules.id;


--
-- Name: m_lamination_production_cost_detail_sizes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_lamination_production_cost_detail_sizes (
    id bigint NOT NULL,
    lamination_production_cost_detail_id bigint NOT NULL,
    lamination_size_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_lamination_production_cost_detail_sizes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_lamination_production_cost_detail_sizes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_lamination_production_cost_detail_sizes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_lamination_production_cost_detail_sizes_id_seq OWNED BY public.m_lamination_production_cost_detail_sizes.id;


--
-- Name: m_lamination_production_cost_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_lamination_production_cost_details (
    id bigint NOT NULL,
    production_cost_id bigint NOT NULL,
    lamination_id bigint NOT NULL,
    production_cost numeric(12,2) NOT NULL,
    finishing_cost numeric(12,2) NOT NULL,
    total_cost numeric(12,2) NOT NULL,
    price_per_meter numeric(12,2) NOT NULL,
    general_price numeric(12,2) NOT NULL,
    division_price numeric(12,2) NOT NULL,
    plain_price numeric(12,2),
    general_pricing_rule_id bigint,
    division_pricing_rule_id bigint,
    plain_pricing_rule_id bigint,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    vendor_id bigint,
    vendor_price numeric(12,2)
);


--
-- Name: m_lamination_production_cost_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_lamination_production_cost_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_lamination_production_cost_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_lamination_production_cost_details_id_seq OWNED BY public.m_lamination_production_cost_details.id;


--
-- Name: m_lamination_sizes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_lamination_sizes (
    id bigint NOT NULL,
    lamination_id bigint NOT NULL,
    width numeric(10,2) NOT NULL,
    length numeric(10,2),
    unit character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: m_lamination_sizes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_lamination_sizes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_lamination_sizes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_lamination_sizes_id_seq OWNED BY public.m_lamination_sizes.id;


--
-- Name: m_laminations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_laminations (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    lamination_code character varying(255)
);


--
-- Name: m_laminations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_laminations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_laminations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_laminations_id_seq OWNED BY public.m_laminations.id;


--
-- Name: m_locations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_locations (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_locations_id_seq OWNED BY public.m_locations.id;


--
-- Name: m_material_sizes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_material_sizes (
    id bigint NOT NULL,
    material_id bigint NOT NULL,
    width numeric(10,2) NOT NULL,
    unit character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    length numeric(10,2)
);


--
-- Name: m_material_sizes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_material_sizes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_material_sizes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_material_sizes_id_seq OWNED BY public.m_material_sizes.id;


--
-- Name: m_materials; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_materials (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    material_code character varying(255),
    material_name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_materials_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_materials_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_materials_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_materials_id_seq OWNED BY public.m_materials.id;


--
-- Name: m_pricing_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_pricing_rules (
    id bigint NOT NULL,
    location_id bigint NOT NULL,
    category_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    markup_percentage numeric(8,2) NOT NULL,
    rounding_value numeric(12,2),
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    engine_id bigint NOT NULL,
    vendor_id bigint
);


--
-- Name: m_pricing_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_pricing_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_pricing_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_pricing_rules_id_seq OWNED BY public.m_pricing_rules.id;


--
-- Name: m_production_cost_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_production_cost_categories (
    id bigint NOT NULL,
    production_cost_id bigint NOT NULL,
    category_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_production_cost_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_production_cost_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_production_cost_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_production_cost_categories_id_seq OWNED BY public.m_production_cost_categories.id;


--
-- Name: m_production_cost_detail_widths; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_production_cost_detail_widths (
    id bigint NOT NULL,
    production_cost_detail_id bigint NOT NULL,
    material_size_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_production_cost_detail_widths_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_production_cost_detail_widths_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_production_cost_detail_widths_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_production_cost_detail_widths_id_seq OWNED BY public.m_production_cost_detail_widths.id;


--
-- Name: m_production_cost_details; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_production_cost_details (
    id bigint NOT NULL,
    production_cost_id bigint NOT NULL,
    material_id bigint NOT NULL,
    production_cost numeric(12,2) NOT NULL,
    finishing_cost numeric(12,2) NOT NULL,
    total_cost numeric(12,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone,
    general_price numeric(12,2) NOT NULL,
    division_price numeric(12,2) NOT NULL,
    plain_price numeric(12,2) NOT NULL,
    price_per_meter numeric(12,2) NOT NULL,
    general_pricing_rule_id bigint,
    division_pricing_rule_id bigint,
    plain_pricing_rule_id bigint,
    vendor_price numeric(12,2),
    vendor_id bigint
);


--
-- Name: m_production_cost_details_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_production_cost_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_production_cost_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_production_cost_details_id_seq OWNED BY public.m_production_cost_details.id;


--
-- Name: m_production_cost_locations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_production_cost_locations (
    id bigint NOT NULL,
    production_cost_id bigint NOT NULL,
    location_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_production_cost_locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_production_cost_locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_production_cost_locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_production_cost_locations_id_seq OWNED BY public.m_production_cost_locations.id;


--
-- Name: m_production_costs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_production_costs (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_production_costs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_production_costs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_production_costs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_production_costs_id_seq OWNED BY public.m_production_costs.id;


--
-- Name: m_sample_galleries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_sample_galleries (
    id bigint NOT NULL,
    engine_id bigint NOT NULL,
    category_id bigint NOT NULL,
    image_path text NOT NULL,
    image_name text NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    image_hash character varying(64)
);


--
-- Name: m_sample_galleries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_sample_galleries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_sample_galleries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_sample_galleries_id_seq OWNED BY public.m_sample_galleries.id;


--
-- Name: m_vendors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.m_vendors (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    contact character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255),
    updated_at timestamp(0) without time zone
);


--
-- Name: m_vendors_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.m_vendors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: m_vendors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.m_vendors_id_seq OWNED BY public.m_vendors.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: t_estimation_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.t_estimation_items (
    id bigint NOT NULL,
    estimation_id bigint NOT NULL,
    category_id bigint NOT NULL,
    material_id bigint NOT NULL,
    material_size_id bigint NOT NULL,
    length numeric(10,2) NOT NULL,
    width numeric(10,2) NOT NULL,
    qty integer NOT NULL,
    objects_per_row integer NOT NULL,
    total_rows integer NOT NULL,
    production_length numeric(10,2) NOT NULL,
    production_area numeric(12,4) NOT NULL,
    billing_area numeric(12,4) NOT NULL,
    material_price numeric(14,2) NOT NULL,
    hpp_per_m2 numeric(14,2) NOT NULL,
    subtotal numeric(14,2) NOT NULL,
    total_hpp numeric(14,2) NOT NULL,
    profit numeric(14,2) NOT NULL,
    margin numeric(8,2) NOT NULL,
    markup numeric(8,2) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: t_estimation_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.t_estimation_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: t_estimation_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.t_estimation_items_id_seq OWNED BY public.t_estimation_items.id;


--
-- Name: t_estimations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.t_estimations (
    id bigint NOT NULL,
    estimation_code character varying(255) NOT NULL,
    location_id bigint NOT NULL,
    engine_id bigint NOT NULL,
    price_type character varying(255) NOT NULL,
    discount_type character varying(255) NOT NULL,
    discount_value numeric(12,2) NOT NULL,
    subtotal numeric(14,2) NOT NULL,
    discount_amount numeric(14,2) NOT NULL,
    grand_total numeric(14,2) NOT NULL,
    total_hpp numeric(14,2) NOT NULL,
    total_profit numeric(14,2) NOT NULL,
    margin numeric(8,2) NOT NULL,
    markup numeric(8,2) NOT NULL,
    status character varying(255) NOT NULL,
    created_by character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_by character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: t_estimations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.t_estimations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: t_estimations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.t_estimations_id_seq OWNED BY public.t_estimations.id;


--
-- Name: user_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_roles (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    role_id bigint NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: user_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_roles_id_seq OWNED BY public.user_roles.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    remember_token character varying(100)
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: m_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_categories ALTER COLUMN id SET DEFAULT nextval('public.m_categories_id_seq'::regclass);


--
-- Name: m_digital_lamination_pricing_rule_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rule_details ALTER COLUMN id SET DEFAULT nextval('public.m_digital_lamination_pricing_rule_details_id_seq'::regclass);


--
-- Name: m_digital_lamination_pricing_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules ALTER COLUMN id SET DEFAULT nextval('public.m_digital_lamination_pricing_rules_id_seq'::regclass);


--
-- Name: m_digital_lamination_production_cost_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details ALTER COLUMN id SET DEFAULT nextval('public.m_digital_lamination_production_cost_details_id_seq'::regclass);


--
-- Name: m_digital_lamination_production_cost_prices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_prices ALTER COLUMN id SET DEFAULT nextval('public.m_digital_lamination_production_cost_prices_id_seq'::regclass);


--
-- Name: m_digital_lamination_production_costs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_costs ALTER COLUMN id SET DEFAULT nextval('public.m_digital_lamination_production_costs_id_seq'::regclass);


--
-- Name: m_digital_pricing_rule_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rule_details ALTER COLUMN id SET DEFAULT nextval('public.m_digital_pricing_rule_details_id_seq'::regclass);


--
-- Name: m_digital_pricing_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules ALTER COLUMN id SET DEFAULT nextval('public.m_digital_pricing_rules_id_seq'::regclass);


--
-- Name: m_digital_production_cost_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_details ALTER COLUMN id SET DEFAULT nextval('public.m_digital_production_cost_details_id_seq'::regclass);


--
-- Name: m_digital_production_cost_prices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_prices ALTER COLUMN id SET DEFAULT nextval('public.m_digital_production_cost_prices_id_seq'::regclass);


--
-- Name: m_digital_production_costs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_costs ALTER COLUMN id SET DEFAULT nextval('public.m_digital_production_costs_id_seq'::regclass);


--
-- Name: m_discount_scopes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes ALTER COLUMN id SET DEFAULT nextval('public.m_discount_scopes_id_seq'::regclass);


--
-- Name: m_discounts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discounts ALTER COLUMN id SET DEFAULT nextval('public.m_discounts_id_seq'::regclass);


--
-- Name: m_display_pricing_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_pricing_rules ALTER COLUMN id SET DEFAULT nextval('public.m_display_pricing_rules_id_seq'::regclass);


--
-- Name: m_display_product_component_prices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_component_prices ALTER COLUMN id SET DEFAULT nextval('public.m_display_product_component_prices_id_seq'::regclass);


--
-- Name: m_display_product_components id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_components ALTER COLUMN id SET DEFAULT nextval('public.m_display_product_components_id_seq'::regclass);


--
-- Name: m_display_product_configurations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_configurations ALTER COLUMN id SET DEFAULT nextval('public.m_display_product_configurations_id_seq'::regclass);


--
-- Name: m_display_product_images id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_images ALTER COLUMN id SET DEFAULT nextval('public.m_display_product_images_id_seq'::regclass);


--
-- Name: m_display_product_prices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_prices ALTER COLUMN id SET DEFAULT nextval('public.m_display_product_prices_id_seq'::regclass);


--
-- Name: m_display_products id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_products ALTER COLUMN id SET DEFAULT nextval('public.m_display_products_id_seq'::regclass);


--
-- Name: m_engines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_engines ALTER COLUMN id SET DEFAULT nextval('public.m_engines_id_seq'::regclass);


--
-- Name: m_lamination_pricing_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules ALTER COLUMN id SET DEFAULT nextval('public.m_lamination_pricing_rules_id_seq'::regclass);


--
-- Name: m_lamination_production_cost_detail_sizes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_detail_sizes ALTER COLUMN id SET DEFAULT nextval('public.m_lamination_production_cost_detail_sizes_id_seq'::regclass);


--
-- Name: m_lamination_production_cost_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details ALTER COLUMN id SET DEFAULT nextval('public.m_lamination_production_cost_details_id_seq'::regclass);


--
-- Name: m_lamination_sizes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_sizes ALTER COLUMN id SET DEFAULT nextval('public.m_lamination_sizes_id_seq'::regclass);


--
-- Name: m_laminations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_laminations ALTER COLUMN id SET DEFAULT nextval('public.m_laminations_id_seq'::regclass);


--
-- Name: m_locations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_locations ALTER COLUMN id SET DEFAULT nextval('public.m_locations_id_seq'::regclass);


--
-- Name: m_material_sizes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_material_sizes ALTER COLUMN id SET DEFAULT nextval('public.m_material_sizes_id_seq'::regclass);


--
-- Name: m_materials id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_materials ALTER COLUMN id SET DEFAULT nextval('public.m_materials_id_seq'::regclass);


--
-- Name: m_pricing_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules ALTER COLUMN id SET DEFAULT nextval('public.m_pricing_rules_id_seq'::regclass);


--
-- Name: m_production_cost_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_categories ALTER COLUMN id SET DEFAULT nextval('public.m_production_cost_categories_id_seq'::regclass);


--
-- Name: m_production_cost_detail_widths id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_detail_widths ALTER COLUMN id SET DEFAULT nextval('public.m_production_cost_detail_widths_id_seq'::regclass);


--
-- Name: m_production_cost_details id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details ALTER COLUMN id SET DEFAULT nextval('public.m_production_cost_details_id_seq'::regclass);


--
-- Name: m_production_cost_locations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_locations ALTER COLUMN id SET DEFAULT nextval('public.m_production_cost_locations_id_seq'::regclass);


--
-- Name: m_production_costs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_costs ALTER COLUMN id SET DEFAULT nextval('public.m_production_costs_id_seq'::regclass);


--
-- Name: m_sample_galleries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_sample_galleries ALTER COLUMN id SET DEFAULT nextval('public.m_sample_galleries_id_seq'::regclass);


--
-- Name: m_vendors id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_vendors ALTER COLUMN id SET DEFAULT nextval('public.m_vendors_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: t_estimation_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items ALTER COLUMN id SET DEFAULT nextval('public.t_estimation_items_id_seq'::regclass);


--
-- Name: t_estimations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimations ALTER COLUMN id SET DEFAULT nextval('public.t_estimations_id_seq'::regclass);


--
-- Name: user_roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_roles ALTER COLUMN id SET DEFAULT nextval('public.user_roles_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: m_digital_lamination_production_cost_details dig_lam_pc_details_unique_lamination_category; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details
    ADD CONSTRAINT dig_lam_pc_details_unique_lamination_category UNIQUE (digital_lamination_production_cost_id, lamination_id, category_id);


--
-- Name: m_digital_lamination_production_cost_prices dig_lam_pc_prices_unique_tier; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_prices
    ADD CONSTRAINT dig_lam_pc_prices_unique_tier UNIQUE (digital_lamination_production_cost_detail_id, price_type, print_type, quantity_tier);


--
-- Name: m_digital_lamination_pricing_rule_details dig_lam_pr_details_unique_tier; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rule_details
    ADD CONSTRAINT dig_lam_pr_details_unique_tier UNIQUE (digital_lamination_pricing_rule_id, price_type, print_type, quantity_tier);


--
-- Name: m_digital_pricing_rule_details digital_pricing_rule_details_unique_tier; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rule_details
    ADD CONSTRAINT digital_pricing_rule_details_unique_tier UNIQUE (digital_pricing_rule_id, price_type, print_type, quantity_tier);


--
-- Name: m_digital_production_cost_details digital_production_cost_details_unique_material; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_details
    ADD CONSTRAINT digital_production_cost_details_unique_material UNIQUE (digital_production_cost_id, material_id);


--
-- Name: m_digital_production_cost_prices digital_production_cost_prices_unique_tier; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_prices
    ADD CONSTRAINT digital_production_cost_prices_unique_tier UNIQUE (digital_production_cost_detail_id, price_type, print_type, quantity_tier);


--
-- Name: m_discount_scopes discount_scopes_unique_configuration; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes
    ADD CONSTRAINT discount_scopes_unique_configuration UNIQUE (discount_id, engine_id, category_id);


--
-- Name: m_discounts discounts_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discounts
    ADD CONSTRAINT discounts_code_unique UNIQUE (code);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: m_categories m_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_categories
    ADD CONSTRAINT m_categories_pkey PRIMARY KEY (id);


--
-- Name: m_digital_lamination_pricing_rule_details m_digital_lamination_pricing_rule_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rule_details
    ADD CONSTRAINT m_digital_lamination_pricing_rule_details_pkey PRIMARY KEY (id);


--
-- Name: m_digital_lamination_pricing_rules m_digital_lamination_pricing_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules
    ADD CONSTRAINT m_digital_lamination_pricing_rules_pkey PRIMARY KEY (id);


--
-- Name: m_digital_lamination_production_cost_details m_digital_lamination_production_cost_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details
    ADD CONSTRAINT m_digital_lamination_production_cost_details_pkey PRIMARY KEY (id);


--
-- Name: m_digital_lamination_production_cost_prices m_digital_lamination_production_cost_prices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_prices
    ADD CONSTRAINT m_digital_lamination_production_cost_prices_pkey PRIMARY KEY (id);


--
-- Name: m_digital_lamination_production_costs m_digital_lamination_production_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_costs
    ADD CONSTRAINT m_digital_lamination_production_costs_pkey PRIMARY KEY (id);


--
-- Name: m_digital_pricing_rule_details m_digital_pricing_rule_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rule_details
    ADD CONSTRAINT m_digital_pricing_rule_details_pkey PRIMARY KEY (id);


--
-- Name: m_digital_pricing_rules m_digital_pricing_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules
    ADD CONSTRAINT m_digital_pricing_rules_pkey PRIMARY KEY (id);


--
-- Name: m_digital_production_cost_details m_digital_production_cost_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_details
    ADD CONSTRAINT m_digital_production_cost_details_pkey PRIMARY KEY (id);


--
-- Name: m_digital_production_cost_prices m_digital_production_cost_prices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_prices
    ADD CONSTRAINT m_digital_production_cost_prices_pkey PRIMARY KEY (id);


--
-- Name: m_digital_production_costs m_digital_production_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_costs
    ADD CONSTRAINT m_digital_production_costs_pkey PRIMARY KEY (id);


--
-- Name: m_discount_scopes m_discount_scopes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes
    ADD CONSTRAINT m_discount_scopes_pkey PRIMARY KEY (id);


--
-- Name: m_discounts m_discounts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discounts
    ADD CONSTRAINT m_discounts_pkey PRIMARY KEY (id);


--
-- Name: m_display_pricing_rules m_display_pricing_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_pricing_rules
    ADD CONSTRAINT m_display_pricing_rules_pkey PRIMARY KEY (id);


--
-- Name: m_display_product_component_prices m_display_product_component_prices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_component_prices
    ADD CONSTRAINT m_display_product_component_prices_pkey PRIMARY KEY (id);


--
-- Name: m_display_product_components m_display_product_components_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_components
    ADD CONSTRAINT m_display_product_components_pkey PRIMARY KEY (id);


--
-- Name: m_display_product_configurations m_display_product_configurations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_configurations
    ADD CONSTRAINT m_display_product_configurations_pkey PRIMARY KEY (id);


--
-- Name: m_display_product_images m_display_product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_images
    ADD CONSTRAINT m_display_product_images_pkey PRIMARY KEY (id);


--
-- Name: m_display_product_prices m_display_product_prices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_prices
    ADD CONSTRAINT m_display_product_prices_pkey PRIMARY KEY (id);


--
-- Name: m_display_products m_display_products_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_products
    ADD CONSTRAINT m_display_products_pkey PRIMARY KEY (id);


--
-- Name: m_engines m_engines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_engines
    ADD CONSTRAINT m_engines_pkey PRIMARY KEY (id);


--
-- Name: m_lamination_pricing_rules m_lamination_pricing_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules
    ADD CONSTRAINT m_lamination_pricing_rules_pkey PRIMARY KEY (id);


--
-- Name: m_lamination_production_cost_detail_sizes m_lamination_production_cost_detail_sizes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_detail_sizes
    ADD CONSTRAINT m_lamination_production_cost_detail_sizes_pkey PRIMARY KEY (id);


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_pkey PRIMARY KEY (id);


--
-- Name: m_lamination_sizes m_lamination_sizes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_sizes
    ADD CONSTRAINT m_lamination_sizes_pkey PRIMARY KEY (id);


--
-- Name: m_laminations m_laminations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_laminations
    ADD CONSTRAINT m_laminations_pkey PRIMARY KEY (id);


--
-- Name: m_locations m_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_locations
    ADD CONSTRAINT m_locations_pkey PRIMARY KEY (id);


--
-- Name: m_lamination_production_cost_detail_sizes m_lpc_detail_size_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_detail_sizes
    ADD CONSTRAINT m_lpc_detail_size_unique UNIQUE (lamination_production_cost_detail_id, lamination_size_id);


--
-- Name: m_material_sizes m_material_sizes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_material_sizes
    ADD CONSTRAINT m_material_sizes_pkey PRIMARY KEY (id);


--
-- Name: m_materials m_materials_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_materials
    ADD CONSTRAINT m_materials_pkey PRIMARY KEY (id);


--
-- Name: m_pricing_rules m_pricing_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules
    ADD CONSTRAINT m_pricing_rules_pkey PRIMARY KEY (id);


--
-- Name: m_production_cost_categories m_production_cost_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_categories
    ADD CONSTRAINT m_production_cost_categories_pkey PRIMARY KEY (id);


--
-- Name: m_production_cost_detail_widths m_production_cost_detail_widths_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_detail_widths
    ADD CONSTRAINT m_production_cost_detail_widths_pkey PRIMARY KEY (id);


--
-- Name: m_production_cost_details m_production_cost_details_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_pkey PRIMARY KEY (id);


--
-- Name: m_production_cost_locations m_production_cost_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_locations
    ADD CONSTRAINT m_production_cost_locations_pkey PRIMARY KEY (id);


--
-- Name: m_production_costs m_production_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_costs
    ADD CONSTRAINT m_production_costs_pkey PRIMARY KEY (id);


--
-- Name: m_sample_galleries m_sample_galleries_engine_category_hash_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_sample_galleries
    ADD CONSTRAINT m_sample_galleries_engine_category_hash_unique UNIQUE (engine_id, category_id, image_hash);


--
-- Name: m_sample_galleries m_sample_galleries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_sample_galleries
    ADD CONSTRAINT m_sample_galleries_pkey PRIMARY KEY (id);


--
-- Name: m_vendors m_vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_vendors
    ADD CONSTRAINT m_vendors_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: t_estimation_items t_estimation_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items
    ADD CONSTRAINT t_estimation_items_pkey PRIMARY KEY (id);


--
-- Name: t_estimations t_estimations_estimation_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimations
    ADD CONSTRAINT t_estimations_estimation_code_unique UNIQUE (estimation_code);


--
-- Name: t_estimations t_estimations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimations
    ADD CONSTRAINT t_estimations_pkey PRIMARY KEY (id);


--
-- Name: user_roles user_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: dig_lam_pc_configuration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dig_lam_pc_configuration_index ON public.m_digital_lamination_production_costs USING btree (engine_id, location_id, vendor_id);


--
-- Name: digital_production_costs_configuration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX digital_production_costs_configuration_index ON public.m_digital_production_costs USING btree (engine_id, location_id, vendor_id);


--
-- Name: discount_scopes_engine_category_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX discount_scopes_engine_category_index ON public.m_discount_scopes USING btree (engine_id, category_id);


--
-- Name: discounts_active_period_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX discounts_active_period_index ON public.m_discounts USING btree (status, starts_at, ends_at);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: m_digital_lamination_production_cost_details dig_lam_pc_details_category_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details
    ADD CONSTRAINT dig_lam_pc_details_category_fk FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_cost_details dig_lam_pc_details_cost_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details
    ADD CONSTRAINT dig_lam_pc_details_cost_fk FOREIGN KEY (digital_lamination_production_cost_id) REFERENCES public.m_digital_lamination_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_cost_details dig_lam_pc_details_lamination_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_details
    ADD CONSTRAINT dig_lam_pc_details_lamination_fk FOREIGN KEY (lamination_id) REFERENCES public.m_laminations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_costs dig_lam_pc_engine_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_costs
    ADD CONSTRAINT dig_lam_pc_engine_fk FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_costs dig_lam_pc_location_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_costs
    ADD CONSTRAINT dig_lam_pc_location_fk FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_cost_prices dig_lam_pc_prices_cost_detail_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_prices
    ADD CONSTRAINT dig_lam_pc_prices_cost_detail_fk FOREIGN KEY (digital_lamination_production_cost_detail_id) REFERENCES public.m_digital_lamination_production_cost_details(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_production_cost_prices dig_lam_pc_prices_pricing_detail_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_cost_prices
    ADD CONSTRAINT dig_lam_pc_prices_pricing_detail_fk FOREIGN KEY (digital_lamination_pricing_rule_detail_id) REFERENCES public.m_digital_lamination_pricing_rule_details(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_digital_lamination_production_costs dig_lam_pc_vendor_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_production_costs
    ADD CONSTRAINT dig_lam_pc_vendor_fk FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_pricing_rule_details dig_lam_pr_details_rule_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rule_details
    ADD CONSTRAINT dig_lam_pr_details_rule_fk FOREIGN KEY (digital_lamination_pricing_rule_id) REFERENCES public.m_digital_lamination_pricing_rules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_cost_details digital_pc_details_cost_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_details
    ADD CONSTRAINT digital_pc_details_cost_fk FOREIGN KEY (digital_production_cost_id) REFERENCES public.m_digital_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_cost_prices digital_pc_prices_cost_detail_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_prices
    ADD CONSTRAINT digital_pc_prices_cost_detail_fk FOREIGN KEY (digital_production_cost_detail_id) REFERENCES public.m_digital_production_cost_details(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_cost_prices digital_pc_prices_pricing_detail_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_prices
    ADD CONSTRAINT digital_pc_prices_pricing_detail_fk FOREIGN KEY (digital_pricing_rule_detail_id) REFERENCES public.m_digital_pricing_rule_details(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_discount_scopes discount_scopes_category_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes
    ADD CONSTRAINT discount_scopes_category_fk FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_discount_scopes discount_scopes_discount_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes
    ADD CONSTRAINT discount_scopes_discount_fk FOREIGN KEY (discount_id) REFERENCES public.m_discounts(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_discount_scopes discount_scopes_engine_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_discount_scopes
    ADD CONSTRAINT discount_scopes_engine_fk FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_digital_lamination_pricing_rules m_digital_lamination_pricing_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules
    ADD CONSTRAINT m_digital_lamination_pricing_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_pricing_rules m_digital_lamination_pricing_rules_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules
    ADD CONSTRAINT m_digital_lamination_pricing_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_pricing_rules m_digital_lamination_pricing_rules_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules
    ADD CONSTRAINT m_digital_lamination_pricing_rules_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_lamination_pricing_rules m_digital_lamination_pricing_rules_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_lamination_pricing_rules
    ADD CONSTRAINT m_digital_lamination_pricing_rules_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_pricing_rule_details m_digital_pricing_rule_details_digital_pricing_rule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rule_details
    ADD CONSTRAINT m_digital_pricing_rule_details_digital_pricing_rule_id_foreign FOREIGN KEY (digital_pricing_rule_id) REFERENCES public.m_digital_pricing_rules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_pricing_rules m_digital_pricing_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules
    ADD CONSTRAINT m_digital_pricing_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_pricing_rules m_digital_pricing_rules_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules
    ADD CONSTRAINT m_digital_pricing_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_pricing_rules m_digital_pricing_rules_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules
    ADD CONSTRAINT m_digital_pricing_rules_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_pricing_rules m_digital_pricing_rules_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_pricing_rules
    ADD CONSTRAINT m_digital_pricing_rules_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_cost_details m_digital_production_cost_details_material_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_cost_details
    ADD CONSTRAINT m_digital_production_cost_details_material_id_foreign FOREIGN KEY (material_id) REFERENCES public.m_materials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_costs m_digital_production_costs_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_costs
    ADD CONSTRAINT m_digital_production_costs_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_costs m_digital_production_costs_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_costs
    ADD CONSTRAINT m_digital_production_costs_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_digital_production_costs m_digital_production_costs_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_digital_production_costs
    ADD CONSTRAINT m_digital_production_costs_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_pricing_rules m_display_pricing_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_pricing_rules
    ADD CONSTRAINT m_display_pricing_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_pricing_rules m_display_pricing_rules_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_pricing_rules
    ADD CONSTRAINT m_display_pricing_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_pricing_rules m_display_pricing_rules_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_pricing_rules
    ADD CONSTRAINT m_display_pricing_rules_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_component_prices m_display_product_component_prices_component_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_component_prices
    ADD CONSTRAINT m_display_product_component_prices_component_id_foreign FOREIGN KEY (component_id) REFERENCES public.m_display_product_components(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_component_prices m_display_product_component_prices_configuration_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_component_prices
    ADD CONSTRAINT m_display_product_component_prices_configuration_id_foreign FOREIGN KEY (configuration_id) REFERENCES public.m_display_product_configurations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_components m_display_product_components_display_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_components
    ADD CONSTRAINT m_display_product_components_display_product_id_foreign FOREIGN KEY (display_product_id) REFERENCES public.m_display_products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_components m_display_product_components_lamination_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_components
    ADD CONSTRAINT m_display_product_components_lamination_id_foreign FOREIGN KEY (lamination_id) REFERENCES public.m_laminations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_components m_display_product_components_material_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_components
    ADD CONSTRAINT m_display_product_components_material_id_foreign FOREIGN KEY (material_id) REFERENCES public.m_materials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_configurations m_display_product_configurations_display_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_configurations
    ADD CONSTRAINT m_display_product_configurations_display_product_id_foreign FOREIGN KEY (display_product_id) REFERENCES public.m_display_products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_configurations m_display_product_configurations_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_configurations
    ADD CONSTRAINT m_display_product_configurations_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_configurations m_display_product_configurations_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_configurations
    ADD CONSTRAINT m_display_product_configurations_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_images m_display_product_images_display_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_images
    ADD CONSTRAINT m_display_product_images_display_product_id_foreign FOREIGN KEY (display_product_id) REFERENCES public.m_display_products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_product_prices m_display_product_prices_display_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_product_prices
    ADD CONSTRAINT m_display_product_prices_display_product_id_foreign FOREIGN KEY (display_product_id) REFERENCES public.m_display_products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_display_products m_display_products_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_display_products
    ADD CONSTRAINT m_display_products_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_pricing_rules m_lamination_pricing_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules
    ADD CONSTRAINT m_lamination_pricing_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_pricing_rules m_lamination_pricing_rules_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules
    ADD CONSTRAINT m_lamination_pricing_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_pricing_rules m_lamination_pricing_rules_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules
    ADD CONSTRAINT m_lamination_pricing_rules_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_pricing_rules m_lamination_pricing_rules_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_pricing_rules
    ADD CONSTRAINT m_lamination_pricing_rules_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_production_cost_detail_sizes m_lamination_production_cost_detail_sizes_lamination_production; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_detail_sizes
    ADD CONSTRAINT m_lamination_production_cost_detail_sizes_lamination_production FOREIGN KEY (lamination_production_cost_detail_id) REFERENCES public.m_lamination_production_cost_details(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_production_cost_detail_sizes m_lamination_production_cost_detail_sizes_lamination_size_id_fo; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_detail_sizes
    ADD CONSTRAINT m_lamination_production_cost_detail_sizes_lamination_size_id_fo FOREIGN KEY (lamination_size_id) REFERENCES public.m_lamination_sizes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_division_pricing_rule_id_f; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_division_pricing_rule_id_f FOREIGN KEY (division_pricing_rule_id) REFERENCES public.m_lamination_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_general_pricing_rule_id_fo; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_general_pricing_rule_id_fo FOREIGN KEY (general_pricing_rule_id) REFERENCES public.m_lamination_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_lamination_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_lamination_id_foreign FOREIGN KEY (lamination_id) REFERENCES public.m_laminations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_plain_pricing_rule_id_fore; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_plain_pricing_rule_id_fore FOREIGN KEY (plain_pricing_rule_id) REFERENCES public.m_lamination_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_production_cost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_production_cost_id_foreign FOREIGN KEY (production_cost_id) REFERENCES public.m_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_production_cost_details m_lamination_production_cost_details_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_production_cost_details
    ADD CONSTRAINT m_lamination_production_cost_details_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_lamination_sizes m_lamination_sizes_lamination_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_lamination_sizes
    ADD CONSTRAINT m_lamination_sizes_lamination_id_foreign FOREIGN KEY (lamination_id) REFERENCES public.m_laminations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_material_sizes m_material_sizes_material_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_material_sizes
    ADD CONSTRAINT m_material_sizes_material_id_foreign FOREIGN KEY (material_id) REFERENCES public.m_materials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_materials m_materials_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_materials
    ADD CONSTRAINT m_materials_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_pricing_rules m_pricing_rules_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules
    ADD CONSTRAINT m_pricing_rules_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_pricing_rules m_pricing_rules_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules
    ADD CONSTRAINT m_pricing_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_pricing_rules m_pricing_rules_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules
    ADD CONSTRAINT m_pricing_rules_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_pricing_rules m_pricing_rules_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_pricing_rules
    ADD CONSTRAINT m_pricing_rules_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_categories m_production_cost_categories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_categories
    ADD CONSTRAINT m_production_cost_categories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_categories m_production_cost_categories_production_cost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_categories
    ADD CONSTRAINT m_production_cost_categories_production_cost_id_foreign FOREIGN KEY (production_cost_id) REFERENCES public.m_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_detail_widths m_production_cost_detail_widths_material_size_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_detail_widths
    ADD CONSTRAINT m_production_cost_detail_widths_material_size_id_foreign FOREIGN KEY (material_size_id) REFERENCES public.m_material_sizes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_detail_widths m_production_cost_detail_widths_production_cost_detail_id_forei; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_detail_widths
    ADD CONSTRAINT m_production_cost_detail_widths_production_cost_detail_id_forei FOREIGN KEY (production_cost_detail_id) REFERENCES public.m_production_cost_details(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_details m_production_cost_details_division_pricing_rule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_division_pricing_rule_id_foreign FOREIGN KEY (division_pricing_rule_id) REFERENCES public.m_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_production_cost_details m_production_cost_details_general_pricing_rule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_general_pricing_rule_id_foreign FOREIGN KEY (general_pricing_rule_id) REFERENCES public.m_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_production_cost_details m_production_cost_details_material_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_material_id_foreign FOREIGN KEY (material_id) REFERENCES public.m_materials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_details m_production_cost_details_plain_pricing_rule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_plain_pricing_rule_id_foreign FOREIGN KEY (plain_pricing_rule_id) REFERENCES public.m_pricing_rules(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: m_production_cost_details m_production_cost_details_production_cost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_production_cost_id_foreign FOREIGN KEY (production_cost_id) REFERENCES public.m_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_details m_production_cost_details_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_details
    ADD CONSTRAINT m_production_cost_details_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.m_vendors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_locations m_production_cost_locations_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_locations
    ADD CONSTRAINT m_production_cost_locations_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_cost_locations m_production_cost_locations_production_cost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_cost_locations
    ADD CONSTRAINT m_production_cost_locations_production_cost_id_foreign FOREIGN KEY (production_cost_id) REFERENCES public.m_production_costs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_production_costs m_production_costs_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_production_costs
    ADD CONSTRAINT m_production_costs_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_sample_galleries m_sample_galleries_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_sample_galleries
    ADD CONSTRAINT m_sample_galleries_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: m_sample_galleries m_sample_galleries_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.m_sample_galleries
    ADD CONSTRAINT m_sample_galleries_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimation_items t_estimation_items_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items
    ADD CONSTRAINT t_estimation_items_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimation_items t_estimation_items_estimation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items
    ADD CONSTRAINT t_estimation_items_estimation_id_foreign FOREIGN KEY (estimation_id) REFERENCES public.t_estimations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimation_items t_estimation_items_material_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items
    ADD CONSTRAINT t_estimation_items_material_id_foreign FOREIGN KEY (material_id) REFERENCES public.m_materials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimation_items t_estimation_items_material_size_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimation_items
    ADD CONSTRAINT t_estimation_items_material_size_id_foreign FOREIGN KEY (material_size_id) REFERENCES public.m_material_sizes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimations t_estimations_engine_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimations
    ADD CONSTRAINT t_estimations_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: t_estimations t_estimations_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.t_estimations
    ADD CONSTRAINT t_estimations_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_roles user_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_roles user_roles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	2026_08_22_144829_create_roles_table	2
3	2026_08_22_144732_create_user_roles_table	3
4	2026_08_22_145746_create_categories_table	4
5	2026_08_22_150228_create_materials_table	5
6	2026_08_22_151249_create_engines_table	6
7	2026_08_22_151944_create_engine_materials_table	7
8	2026_08_22_153028_create_vendors_table	8
9	2026_08_22_153240_create_material_vendors_table	9
10	2026_08_22_162754_create_engine_rates_table	10
11	2026_08_22_163502_create_finishing_services_table	11
12	2026_08_22_164149_create_price_lists_table	12
13	2026_08_22_171236_create_sessions_table	13
14	2026_08_23_060615_create_drop_all_tables	14
15	2026_08_23_061806_create_drop_user_and_user_roles_table	15
16	2026_08_23_061953_create_users_table	16
17	2026_08_23_062237_create_user_roles_table	17
18	2026_08_23_064407_create_categories_table	18
19	2026_08_23_064746_create_engines_table	19
20	2026_08_23_065507_create_drop_all_table	20
21	0001_01_01_000001_create_cache_table	21
22	0001_01_01_000002_create_jobs_table	21
23	2026_08_23_080003_create_m_engines_table	21
24	2026_08_23_080627_create_m_vendors_table	21
25	2026_08_23_080839_create_m_categories_table	22
26	2026_08_23_081425_create_users_table	23
27	2026_08_23_081611_create_roles_table	24
28	2026_08_23_081802_create_user_roles_table	24
29	2026_08_23_083151_create_m_calculation_rules_table	25
30	2026_08_23_083229_create_m_pricing_policies_table	25
31	2026_08_23_083817_create_m_engine_rates_table	25
32	2026_08_23_090453_create_m_materials_table	26
33	2026_08_23_085756_create_m_pricing_lists_table	27
34	2026_08_23_091356_create_m_services_table	27
35	2026_09_03_031556_remove_unused_tables	28
36	2026_09_03_052944_create_m_materials_table	29
37	2026_09_03_041226_create_m_engine_materials_table	30
38	2026_09_03_055555_create_m_locations_table	31
39	2026_09_05_015231_create_m_material_sizes_table	32
40	2026_09_05_091136_create_m_production_costs_table	33
41	2026_09_06_091635_update_m_production_costs_add_finishing_cost	34
42	2026_09_07_065209_add_length_and_price_to_m_material_sizes_table	35
43	2026_09_07_081117_create_m_material_prices_table	36
44	2026_09_08_122545_add_pricing_columns_to_m_production_costs_table	37
45	2026_09_08_124002_add_price_per_meter_to_m_materials_table	38
46	2026_09_09_020643_create_m_calculation_rules_table	39
47	2026_09_09_030940_update_m_calculation_rules_table	40
48	2026_09_09_050553_remove_unique_from_code_in_m_calculation_rules_table	41
49	2026_09_10_010322_create_t_estimations_table	42
50	2026_09_10_011717_add_unique_to_estimation_code_in_t_estimations_table	43
51	2026_09_10_012004_create_t_estimation_items_table	44
52	2026_09_10_065419_update_m_engines_table	45
53	2026_09_10_090930_remove_price_per_meter_from_m_materials_table	46
54	2026_09_11_054845_simplify_m_production_costs_table	47
55	2026_09_11_055219_create_m_production_cost_locations_table	48
56	2026_09_11_055553_create_m_production_cost_details_table	48
57	2026_09_11_060037_create_m_production_cost_detail_widths_table	48
58	2026_09_11_075505_add_price_columns_to_m_production_cost_details_table	49
59	2026_09_11_081947_add_price_per_meter_to_m_production_cost_details_table	50
60	2026_09_11_104657_update_m_material_sizes_table	51
61	2026_09_14_133809_add_length_to_m_material_sizes_table	52
62	2026_09_15_115820_change_length_to_integer_on_m_material_sizes_table	53
63	2026_09_16_022552_change_width_and_length_to_decimal_on_m_material_sizes_table	54
64	2026_09_16_040515_create_m_pricing_rules_table	55
65	2026_09_17_044140_add_pricing_rule_foreign_keys_to_m_production_cost_details_table	56
66	2026_09_17_141641_create_m_laminations_table	57
67	2026_09_17_142106_create_m_lamination_configurations_table	58
68	2026_09_18_031112_add_column_category_id_to_m_lamination_configurations_table	59
69	2026_09_18_160506_alter_m_laminations_nullable_updated_fields	60
70	2026_09_18_162217_restore_m_laminations_structure	61
71	2026_09_18_164351_create_m_lamination_sizes_table	62
72	2026_09_19_072828_add_column_engine_id_to_m_pricing_rules_table	63
73	2026_09_20_022155_add_column_to_m_laminations_table	64
74	2026_09_20_081240_create_m_lamination_pricing_rules_table	65
75	2026_09_20_104100_create_m_lamination_categories_table	66
76	2026_09_20_105949_create_m_lamination_production_cost_details_table	67
77	2026_09_20_105640_create_m_lamination_production_cost_detail_sizes_table	68
78	2026_09_20_113159_drop_column_category_id_to_m_laminations_table	69
79	2026_09_20_144235_add_column_to_m_lamination_pricing_rules	70
80	2026_09_21_070934_drop_m_lamination_categories_table	71
81	2026_09_21_071635_create_m_production_cost_categories_table	72
82	2026_09_21_071922_add_unique_to_m_lamination_production_cost_detail_sizes_table	73
83	2026_09_21_135344_update_column_to_m_lamination_production_cost_details_table	74
84	2026_09_23_080059_add_column_vendor_price_to_m_production_cost_details	75
85	2026_09_23_084044_add_column_vendor_id_to_m_production_cost_details	76
86	2026_09_23_145542_add_column_vendor_id_to_m_pricing_rules	77
87	2026_09_26_144618_add_column_vendor_id_to_m_lamination_pricing_rules_table	78
88	2026_09_27_042233_add_column_to_m_lamination_production_cost_details_table	79
89	2026_09_29_044622_create_m_display_products_table	80
90	2026_09_29_045611_create_m_display_product_components_table	81
91	2026_09_29_051749_add_column_rangka_to_m_display_products_table	82
92	2026_09_29_064359_create_m_display_product_images_table	83
93	2026_09_29_081130_modify_column_to_m_display_products_table	84
94	2026_09_29_091403_add_foreign_key_to_m_display_product_images_table	85
95	2026_09_30_034839_update_column_to_m_display_product_components	86
96	2026_09_30_040049_create_m_display_product_configurations_table	87
97	2026_09_30_041333_create_m_display_pricing_rules_table	88
98	2026_09_30_071539_add_column_category_id_to_m_display_products_table	89
99	2026_09_30_083211_add_column_plain_price_to_m_display_product_configurations_table	90
100	2026_09_30_085951_modify_column_to_m_display_pricing_rules_table	91
101	2026_09_30_144635_modify_column_rounding_value_to_m_display_pricing_rules_table	92
102	2026_10_01_021515_modify_column_to_m_display_product_components_table	93
103	2026_10_01_022727_modify_column_to_m_display_product_configurations_table	94
104	2026_10_01_023610_create_m_display_product_component_prices_table	95
105	2026_10_01_065032_modify_column_to_m_display_product_components_table	96
106	2026_10_01_065908_create_m_display_product_prices_table	97
107	2026_10_01_071510_modify_column_to_m_display_product_prices_table	98
108	2026_10_03_051641_update_column_to_m_display_product_component_prices_table	99
109	2026_10_05_023958_create_m_sample_galleries_table	100
110	2026_10_05_032351_modify_column_to_m_sample_galleries_table	101
111	2026_10_10_092121_create_m_digital_pricing_rules_table	102
112	2026_10_10_092633_create_m_digital_pricing_rule_details_table	102
113	2026_10_10_094324_modify_to_m_digital_pricing_rules_table	102
114	2026_10_11_084022_create_m_digital_lamination_pricing_rules_table	102
115	2026_10_11_084100_create_m_digital_lamination_pricing_rule_details_table	102
116	2026_10_12_093132_create_m_digital_production_costs_table	102
117	2026_10_12_093200_create_m_digital_production_cost_details_table	102
118	2026_10_12_093224_create_m_digital_production_cost_prices_table	102
119	2026_10_13_083837_create_m_digital_lamination_production_costs_table	102
120	2026_10_13_083859_create_m_digital_lamination_production_cost_details_table	102
121	2026_10_13_083929_create_m_digital_lamination_production_cost_prices_table	102
122	2026_10_14_155722_create_m_discounts_table	102
123	2026_10_14_155746_create_m_discount_scopes_table	102
124	2026_10_14_175203_add_code_to_m_discounts_table	102
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 124, true);


--
-- PostgreSQL database dump complete
--

