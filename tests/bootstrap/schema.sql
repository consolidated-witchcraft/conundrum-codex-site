--
-- PostgreSQL database dump
--

\restrict fqxCLtxOUNjEgNgbRpJDDwR0Plb7WMclJQ6GZeOh5MKacjAtqST7kd8Gn2bOfTH

-- Dumped from database version 16.13
-- Dumped by pg_dump version 16.13

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: abuse_report; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.abuse_report (
    id integer NOT NULL,
    reported_user_id integer NOT NULL,
    reporting_user_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    reason character varying(1024) NOT NULL,
    notes character varying(1024) DEFAULT NULL::character varying,
    checked_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    confirmed_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN abuse_report.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.abuse_report.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN abuse_report.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.abuse_report.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN abuse_report.checked_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.abuse_report.checked_date IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN abuse_report.confirmed_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.abuse_report.confirmed_date IS '(DC2Type:datetime_immutable)';


--
-- Name: abuse_report_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.abuse_report_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: abuse_report_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.abuse_report_id_seq OWNED BY public.abuse_report.id;


--
-- Name: blocked_email_address; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.blocked_email_address (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    email_address character varying(255) NOT NULL,
    block_reason character varying(255) NOT NULL
);


--
-- Name: COLUMN blocked_email_address.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.blocked_email_address.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN blocked_email_address.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.blocked_email_address.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: blocked_email_address_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.blocked_email_address_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: blocked_email_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.blocked_email_address_id_seq OWNED BY public.blocked_email_address.id;


--
-- Name: bug_report; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.bug_report (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    summary character varying(255) NOT NULL,
    reporter_name character varying(255) NOT NULL,
    reporter_email character varying(255) NOT NULL,
    text text NOT NULL,
    referring_url character varying(255) DEFAULT NULL::character varying,
    actioned_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    closed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN bug_report.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.bug_report.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN bug_report.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.bug_report.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN bug_report.actioned_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.bug_report.actioned_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN bug_report.closed_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.bug_report.closed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: bug_report_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.bug_report_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: bug_report_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.bug_report_id_seq OWNED BY public.bug_report.id;


--
-- Name: campaign; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campaign (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description character varying(1024) NOT NULL,
    status character varying(255) NOT NULL,
    start_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN campaign.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.campaign.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN campaign.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.campaign.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN campaign.start_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.campaign.start_date IS '(DC2Type:datetime_immutable)';


--
-- Name: campaign_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.campaign_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campaign_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.campaign_id_seq OWNED BY public.campaign.id;


--
-- Name: campaign_membership; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campaign_membership (
    id integer NOT NULL,
    campaign_id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    type character varying(255) NOT NULL
);


--
-- Name: COLUMN campaign_membership.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.campaign_membership.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN campaign_membership.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.campaign_membership.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: campaign_membership_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.campaign_membership_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campaign_membership_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.campaign_membership_id_seq OWNED BY public.campaign_membership.id;


--
-- Name: casebook; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook (
    id integer NOT NULL,
    game_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    brief character varying(2048) NOT NULL,
    publication_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN casebook.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_id_seq OWNED BY public.casebook.id;


--
-- Name: casebook_subject; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook_subject (
    id integer NOT NULL,
    casebook_id integer NOT NULL,
    casebook_subject_type_id integer NOT NULL,
    casebook_subject_image_file_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(2048) NOT NULL,
    revealed_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN casebook_subject.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject.revealed_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject.revealed_date IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_subject_clue; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook_subject_clue (
    id integer NOT NULL,
    type_id integer NOT NULL,
    casebook_subject_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    title character varying(255) NOT NULL,
    body character varying(1024) NOT NULL,
    revealed_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN casebook_subject_clue.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_clue.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject_clue.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_clue.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject_clue.revealed_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_clue.revealed_date IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_subject_clue_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_subject_clue_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_subject_clue_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_subject_clue_id_seq OWNED BY public.casebook_subject_clue.id;


--
-- Name: casebook_subject_clue_type; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook_subject_clue_type (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL
);


--
-- Name: COLUMN casebook_subject_clue_type.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_clue_type.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject_clue_type.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_clue_type.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_subject_clue_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_subject_clue_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_subject_clue_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_subject_clue_type_id_seq OWNED BY public.casebook_subject_clue_type.id;


--
-- Name: casebook_subject_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_subject_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_subject_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_subject_id_seq OWNED BY public.casebook_subject.id;


--
-- Name: casebook_subject_note; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook_subject_note (
    id integer NOT NULL,
    created_by_id integer NOT NULL,
    casebook_subject_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    title character varying(255) NOT NULL,
    body character varying(1024) NOT NULL
);


--
-- Name: COLUMN casebook_subject_note.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_note.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject_note.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_note.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_subject_note_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_subject_note_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_subject_note_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_subject_note_id_seq OWNED BY public.casebook_subject_note.id;


--
-- Name: casebook_subject_type; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.casebook_subject_type (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL
);


--
-- Name: COLUMN casebook_subject_type.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_type.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN casebook_subject_type.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.casebook_subject_type.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: casebook_subject_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.casebook_subject_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: casebook_subject_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.casebook_subject_type_id_seq OWNED BY public.casebook_subject_type.id;


--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


--
-- Name: event; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.event (
    id integer NOT NULL,
    hero_image_id integer,
    location_id integer,
    organisation_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    title character varying(255) NOT NULL,
    event_slug character varying(255) NOT NULL,
    description character varying(2048) NOT NULL,
    prebook_only boolean NOT NULL,
    start_date timestamp(0) without time zone NOT NULL,
    end_date timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN event.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.event.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN event.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.event.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: event_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.event_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.event_id_seq OWNED BY public.event.id;


--
-- Name: feature_switcher; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feature_switcher (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    handle character varying(255) NOT NULL,
    label character varying(255) NOT NULL,
    enabled boolean NOT NULL
);


--
-- Name: COLUMN feature_switcher.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.feature_switcher.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN feature_switcher.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.feature_switcher.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: feature_switcher_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feature_switcher_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feature_switcher_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feature_switcher_id_seq OWNED BY public.feature_switcher.id;


--
-- Name: game; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.game (
    id integer NOT NULL,
    games_master_id integer NOT NULL,
    hero_image_file_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description character varying(1024) NOT NULL,
    archived_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN game.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN game.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: game_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.game_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: game_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.game_id_seq OWNED BY public.game.id;


--
-- Name: game_invitation; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.game_invitation (
    id integer NOT NULL,
    game_id integer NOT NULL,
    user_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    invitation_code character varying(64) NOT NULL,
    email character varying(255) NOT NULL,
    invitation_message character varying(255) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    date_used date
);


--
-- Name: COLUMN game_invitation.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_invitation.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN game_invitation.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_invitation.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN game_invitation.expires_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_invitation.expires_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN game_invitation.date_used; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_invitation.date_used IS '(DC2Type:date_immutable)';


--
-- Name: game_invitation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.game_invitation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: game_invitation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.game_invitation_id_seq OWNED BY public.game_invitation.id;


--
-- Name: game_session; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.game_session (
    id integer NOT NULL,
    game_id integer NOT NULL,
    session_image_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    session_date timestamp(0) without time zone NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    cancellation_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN game_session.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_session.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN game_session.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.game_session.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: game_session_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.game_session_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: game_session_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.game_session_id_seq OWNED BY public.game_session.id;


--
-- Name: game_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.game_user (
    game_id integer NOT NULL,
    user_id integer NOT NULL
);


--
-- Name: image_type_format_map; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.image_type_format_map (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    managed_image_type character varying(255) NOT NULL
);


--
-- Name: COLUMN image_type_format_map.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.image_type_format_map.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN image_type_format_map.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.image_type_format_map.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: image_type_format_map_derivative_format; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.image_type_format_map_derivative_format (
    image_type_format_map_id integer NOT NULL,
    managed_image_derivative_format_id integer NOT NULL
);


--
-- Name: image_type_format_map_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.image_type_format_map_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: image_type_format_map_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.image_type_format_map_id_seq OWNED BY public.image_type_format_map.id;


--
-- Name: managed_file; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.managed_file (
    id integer NOT NULL,
    uploader_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    path character varying(1024) NOT NULL,
    mime_type character varying(128) NOT NULL,
    title character varying(255) NOT NULL,
    description character varying(512) NOT NULL,
    file_hash character varying(64) DEFAULT NULL::character varying,
    is_orphaned boolean DEFAULT false NOT NULL
);


--
-- Name: COLUMN managed_file.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_file.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN managed_file.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_file.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: managed_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.managed_file_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: managed_file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.managed_file_id_seq OWNED BY public.managed_file.id;


--
-- Name: managed_image; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.managed_image (
    id integer NOT NULL,
    created_by_id integer NOT NULL,
    source_image_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    description character varying(255) NOT NULL,
    managed_image_type character varying(255) DEFAULT 'default'::character varying NOT NULL
);


--
-- Name: COLUMN managed_image.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN managed_image.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: managed_image_derivative; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.managed_image_derivative (
    id integer NOT NULL,
    parent_image_id integer NOT NULL,
    format_id integer NOT NULL,
    derivative_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN managed_image_derivative.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image_derivative.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN managed_image_derivative.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image_derivative.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: managed_image_derivative_format; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.managed_image_derivative_format (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    handle character varying(255) NOT NULL,
    resolution_x integer NOT NULL,
    quality integer NOT NULL,
    resolution_y integer,
    allow_crop boolean DEFAULT false NOT NULL,
    centre_image boolean DEFAULT false NOT NULL
);


--
-- Name: COLUMN managed_image_derivative_format.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image_derivative_format.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN managed_image_derivative_format.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.managed_image_derivative_format.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: managed_image_derivative_format_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.managed_image_derivative_format_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: managed_image_derivative_format_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.managed_image_derivative_format_id_seq OWNED BY public.managed_image_derivative_format.id;


--
-- Name: managed_image_derivative_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.managed_image_derivative_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: managed_image_derivative_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.managed_image_derivative_id_seq OWNED BY public.managed_image_derivative.id;


--
-- Name: managed_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.managed_image_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: managed_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.managed_image_id_seq OWNED BY public.managed_image.id;


--
-- Name: order_charge_line; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.order_charge_line (
    id integer NOT NULL,
    order_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    charge_type character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    price_centessimal integer NOT NULL
);


--
-- Name: COLUMN order_charge_line.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_charge_line.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN order_charge_line.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_charge_line.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: order_charge_line_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.order_charge_line_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_charge_line_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.order_charge_line_id_seq OWNED BY public.order_charge_line.id;


--
-- Name: order_product_line; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.order_product_line (
    id integer NOT NULL,
    order_id integer,
    product_variant_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    quantity integer NOT NULL
);


--
-- Name: COLUMN order_product_line.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_product_line.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN order_product_line.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_product_line.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: order_product_line_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.order_product_line_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_product_line_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.order_product_line_id_seq OWNED BY public.order_product_line.id;


--
-- Name: order_tax; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.order_tax (
    id integer NOT NULL,
    order_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    tax_rate_public_reference character varying(255) NOT NULL,
    tax_rate_name character varying(255) NOT NULL,
    tax_rate_handle character varying(255) NOT NULL,
    amount_centessimal integer NOT NULL
);


--
-- Name: COLUMN order_tax.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_tax.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN order_tax.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.order_tax.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: order_tax_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.order_tax_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_tax_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.order_tax_id_seq OWNED BY public.order_tax.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.orders (
    id integer NOT NULL,
    user_id integer,
    delivery_address_id integer,
    billing_address_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    order_reference character varying(255) NOT NULL,
    order_status character varying(255) NOT NULL
);


--
-- Name: COLUMN orders.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.orders.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN orders.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.orders.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: organisation; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.organisation (
    id integer NOT NULL,
    organisation_logo_id integer NOT NULL,
    owner_id integer,
    postal_address_id integer,
    invoice_address_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    handle character varying(255) NOT NULL,
    deactivation_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN organisation.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.organisation.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN organisation.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.organisation.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: organisation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.organisation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: organisation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.organisation_id_seq OWNED BY public.organisation.id;


--
-- Name: payment; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.payment (
    id integer NOT NULL,
    order_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    payment_amount_centessimal integer NOT NULL,
    payment_reference character varying(255) NOT NULL,
    payment_status character varying(255) NOT NULL
);


--
-- Name: COLUMN payment.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.payment.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN payment.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.payment.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: payment_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.payment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: payment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.payment_id_seq OWNED BY public.payment.id;


--
-- Name: payment_receipt; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.payment_receipt (
    id integer NOT NULL,
    payment_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    receipt_reference character varying(255) NOT NULL,
    amount_centessimal integer NOT NULL,
    receipt_date timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN payment_receipt.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.payment_receipt.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN payment_receipt.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.payment_receipt.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN payment_receipt.receipt_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.payment_receipt.receipt_date IS '(DC2Type:datetime_immutable)';


--
-- Name: payment_receipt_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.payment_receipt_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: payment_receipt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.payment_receipt_id_seq OWNED BY public.payment_receipt.id;


--
-- Name: permission; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permission (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL,
    description character varying(255) NOT NULL
);


--
-- Name: COLUMN permission.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.permission.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN permission.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.permission.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: permission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permission_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permission_id_seq OWNED BY public.permission.id;


--
-- Name: play_event; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.play_event (
    id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    type character varying(255) NOT NULL,
    puzzle_instance_code character varying(255) NOT NULL,
    puzzle_template_slug character varying(255) NOT NULL
);


--
-- Name: COLUMN play_event.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.play_event.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN play_event.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.play_event.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: play_event_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.play_event_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: play_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.play_event_id_seq OWNED BY public.play_event.id;


--
-- Name: postal_address; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.postal_address (
    id integer NOT NULL,
    user_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    address_line_one character varying(255) NOT NULL,
    town_city character varying(255) NOT NULL,
    postal_zip character varying(255) NOT NULL,
    country_code character varying(255) NOT NULL,
    county_province character varying(255) NOT NULL,
    is_default_billing_address boolean NOT NULL,
    is_default_shipping_address boolean NOT NULL,
    address_line_two character varying(255) DEFAULT NULL::character varying,
    latitude double precision,
    longitude double precision
);


--
-- Name: COLUMN postal_address.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.postal_address.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN postal_address.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.postal_address.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: postal_address_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.postal_address_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: postal_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.postal_address_id_seq OWNED BY public.postal_address.id;


--
-- Name: product; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product (
    id integer NOT NULL,
    organisation_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    base_sku character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(2198) NOT NULL
);


--
-- Name: COLUMN product.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN product.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: product_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_id_seq OWNED BY public.product.id;


--
-- Name: product_variant; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_variant (
    id integer NOT NULL,
    created_by_id integer,
    product_variant_stock_id integer,
    product_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    sku character varying(255) NOT NULL,
    product_variant_name character varying(255) NOT NULL,
    type character varying(255) DEFAULT 'e_ticket'::character varying NOT NULL,
    delta integer NOT NULL,
    pre_tax_price_centessimal integer NOT NULL,
    publication_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    tax_exempt boolean NOT NULL,
    requires_postage boolean NOT NULL,
    retired_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN product_variant.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_variant.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN product_variant.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_variant.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: product_variant_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_variant_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_variant_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_variant_id_seq OWNED BY public.product_variant.id;


--
-- Name: product_variant_managed_image; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_variant_managed_image (
    product_variant_id integer NOT NULL,
    managed_image_id integer NOT NULL
);


--
-- Name: product_variant_stock; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_variant_stock (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    stock_level integer NOT NULL
);


--
-- Name: COLUMN product_variant_stock.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_variant_stock.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN product_variant_stock.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_variant_stock.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: product_variant_stock_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_variant_stock_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_variant_stock_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_variant_stock_id_seq OWNED BY public.product_variant_stock.id;


--
-- Name: puzzle_category; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.puzzle_category (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL,
    description character varying(255) NOT NULL
);


--
-- Name: COLUMN puzzle_category.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.puzzle_category.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN puzzle_category.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.puzzle_category.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: puzzle_category_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.puzzle_category_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: puzzle_category_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.puzzle_category_id_seq OWNED BY public.puzzle_category.id;


--
-- Name: puzzle_instance; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.puzzle_instance (
    id integer NOT NULL,
    game_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    instance_code character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(2048) NOT NULL,
    template_slug character varying(1024) NOT NULL,
    publication_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    config json NOT NULL
);


--
-- Name: COLUMN puzzle_instance.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.puzzle_instance.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN puzzle_instance.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.puzzle_instance.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: puzzle_instance_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.puzzle_instance_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: puzzle_instance_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.puzzle_instance_id_seq OWNED BY public.puzzle_instance.id;


--
-- Name: quotation; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.quotation (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    quotation character varying(1024) NOT NULL,
    citation character varying(255) NOT NULL
);


--
-- Name: COLUMN quotation.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.quotation.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN quotation.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.quotation.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: quotation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.quotation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: quotation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.quotation_id_seq OWNED BY public.quotation.id;


--
-- Name: reset_password_request; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reset_password_request (
    id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    selector character varying(20) NOT NULL,
    hashed_token character varying(100) NOT NULL,
    requested_at timestamp(0) without time zone NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN reset_password_request.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.reset_password_request.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN reset_password_request.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.reset_password_request.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN reset_password_request.requested_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.reset_password_request.requested_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN reset_password_request.expires_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.reset_password_request.expires_at IS '(DC2Type:datetime_immutable)';


--
-- Name: reset_password_request_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reset_password_request_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reset_password_request_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reset_password_request_id_seq OWNED BY public.reset_password_request.id;


--
-- Name: role; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL
);


--
-- Name: COLUMN role.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.role.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN role.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.role.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.role_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.role_id_seq OWNED BY public.role.id;


--
-- Name: role_permission; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_permission (
    role_id integer NOT NULL,
    permission_id integer NOT NULL
);


--
-- Name: ticket; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ticket (
    id integer NOT NULL,
    ticket_type_id integer,
    user_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    customer_email character varying(255) NOT NULL,
    customer_name character varying(255) NOT NULL,
    ticket_code character varying(255) NOT NULL,
    issued_date timestamp(0) without time zone NOT NULL,
    paid_date timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN ticket.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN ticket.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN ticket.issued_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket.issued_date IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN ticket.paid_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket.paid_date IS '(DC2Type:datetime_immutable)';


--
-- Name: ticket_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ticket_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ticket_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ticket_id_seq OWNED BY public.ticket.id;


--
-- Name: ticket_type; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ticket_type (
    id integer NOT NULL,
    event_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    price_centessimal integer NOT NULL,
    refundable_until timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN ticket_type.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket_type.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN ticket_type.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket_type.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: ticket_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ticket_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ticket_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ticket_type_id_seq OWNED BY public.ticket_type.id;


--
-- Name: ticket_type_product_variant_map; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ticket_type_product_variant_map (
    id integer NOT NULL,
    ticket_type_id integer,
    product_variant_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN ticket_type_product_variant_map.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket_type_product_variant_map.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN ticket_type_product_variant_map.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.ticket_type_product_variant_map.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: ticket_type_product_variant_map_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ticket_type_product_variant_map_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ticket_type_product_variant_map_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ticket_type_product_variant_map_id_seq OWNED BY public.ticket_type_product_variant_map.id;


--
-- Name: user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    user_account_type_id integer NOT NULL,
    organisation_id integer,
    avatar_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    email character varying(255) NOT NULL,
    username character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    preferences_has_accepted_cookies boolean NOT NULL,
    preferences_profile_public boolean NOT NULL,
    preferences_preferred_font_stack character varying(255) DEFAULT 'regular'::character varying NOT NULL
);


--
-- Name: COLUMN "user".created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public."user".created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN "user".updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public."user".updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_access_token; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_access_token (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    user_identifier character varying(1024) NOT NULL,
    token character varying(128) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN user_access_token.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_access_token.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_access_token.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_access_token.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_access_token.expires_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_access_token.expires_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_access_token_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_access_token_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_access_token_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_access_token_id_seq OWNED BY public.user_access_token.id;


--
-- Name: user_account_type; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_account_type (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL,
    maximum_concurrent_games integer DEFAULT 5 NOT NULL,
    description character varying(255) NOT NULL
);


--
-- Name: COLUMN user_account_type.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_account_type.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_account_type.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_account_type.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_account_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_account_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_account_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_account_type_id_seq OWNED BY public.user_account_type.id;


--
-- Name: user_block; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_block (
    id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    reason character varying(255) NOT NULL,
    expiration_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN user_block.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_block.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_block.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_block.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_block.expiration_date; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_block.expiration_date IS '(DC2Type:datetime_immutable)';


--
-- Name: user_block_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_block_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_block_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_block_id_seq OWNED BY public.user_block.id;


--
-- Name: user_cart; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_cart (
    id integer NOT NULL,
    user_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: COLUMN user_cart.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_cart.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_cart_charge; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_cart_charge (
    id integer NOT NULL,
    user_cart_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    charge_type character varying(255) NOT NULL,
    pre_tax_amount_centessimal integer NOT NULL
);


--
-- Name: COLUMN user_cart_charge.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart_charge.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_cart_charge.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart_charge.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_cart_charge_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_cart_charge_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_cart_charge_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_cart_charge_id_seq OWNED BY public.user_cart_charge.id;


--
-- Name: user_cart_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_cart_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_cart_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_cart_id_seq OWNED BY public.user_cart.id;


--
-- Name: user_cart_product_line; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_cart_product_line (
    id integer NOT NULL,
    user_cart_id integer,
    product_variant_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    quantity integer NOT NULL
);


--
-- Name: COLUMN user_cart_product_line.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart_product_line.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_cart_product_line.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_cart_product_line.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_cart_product_line_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_cart_product_line_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_cart_product_line_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_cart_product_line_id_seq OWNED BY public.user_cart_product_line.id;


--
-- Name: user_feat; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_feat (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    label character varying(255) NOT NULL,
    handle character varying(255) NOT NULL,
    description character varying(512) NOT NULL,
    icon_class character varying(128) NOT NULL,
    rarity character varying(255) NOT NULL,
    games_master_awardable boolean NOT NULL
);


--
-- Name: COLUMN user_feat.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_feat.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN user_feat.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.user_feat.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user_feat_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_feat_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_feat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_feat_id_seq OWNED BY public.user_feat.id;


--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: user_role; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_role (
    user_id integer NOT NULL,
    role_id integer NOT NULL
);


--
-- Name: user_user_feat; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_user_feat (
    user_id integer NOT NULL,
    user_feat_id integer NOT NULL
);


--
-- Name: user_world; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_world (
    user_id integer NOT NULL,
    world_id integer NOT NULL
);


--
-- Name: world; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world (
    id integer NOT NULL,
    creator_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(2198) NOT NULL,
    slug character varying(255) NOT NULL
);


--
-- Name: COLUMN world.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_calendar_day_of_week; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_calendar_day_of_week (
    id integer NOT NULL,
    world_calendar_settings_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    abbreviation character varying(4) NOT NULL
);


--
-- Name: COLUMN world_calendar_day_of_week.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_day_of_week.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_calendar_day_of_week.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_day_of_week.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_calendar_day_of_week_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_calendar_day_of_week_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_calendar_day_of_week_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_calendar_day_of_week_id_seq OWNED BY public.world_calendar_day_of_week.id;


--
-- Name: world_calendar_month; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_calendar_month (
    id integer NOT NULL,
    world_calendar_settings_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    name character varying(255) NOT NULL,
    abbreviation character varying(3) NOT NULL,
    delta integer NOT NULL,
    number_of_days integer NOT NULL
);


--
-- Name: COLUMN world_calendar_month.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_month.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_calendar_month.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_month.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_calendar_month_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_calendar_month_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_calendar_month_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_calendar_month_id_seq OWNED BY public.world_calendar_month.id;


--
-- Name: world_calendar_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_calendar_settings (
    id integer NOT NULL,
    world_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    hours_in_day integer DEFAULT 24 NOT NULL
);


--
-- Name: COLUMN world_calendar_settings.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_settings.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_calendar_settings.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_calendar_settings.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_calendar_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_calendar_settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_calendar_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_calendar_settings_id_seq OWNED BY public.world_calendar_settings.id;


--
-- Name: world_edge; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_edge (
    id integer NOT NULL,
    from_entity_id integer NOT NULL,
    to_entity_id integer NOT NULL,
    source_revision_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    relationship_type character varying(100) NOT NULL,
    revealed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    valid_from timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    valid_to timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    description text,
    is_inferred boolean DEFAULT false NOT NULL
);


--
-- Name: COLUMN world_edge.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_edge.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_edge.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_edge.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_edge.revealed_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_edge.revealed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_edge.valid_from; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_edge.valid_from IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_edge.valid_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_edge.valid_to IS '(DC2Type:datetime_immutable)';


--
-- Name: world_edge_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_edge_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_edge_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_edge_id_seq OWNED BY public.world_edge.id;


--
-- Name: world_entity; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_entity (
    id integer NOT NULL,
    current_revision_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    slug character varying(255) NOT NULL,
    current_name character varying(255) NOT NULL,
    revealed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    status character varying(32) DEFAULT 'canonical'::character varying NOT NULL,
    entity_type character varying(255) NOT NULL
);


--
-- Name: COLUMN world_entity.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_entity.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_entity.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_entity.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_entity.revealed_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_entity.revealed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_entity_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_entity_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_entity_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_entity_id_seq OWNED BY public.world_entity.id;


--
-- Name: world_entity_revision; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_entity_revision (
    id integer NOT NULL,
    entity_id integer NOT NULL,
    created_by_id integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    revision_number integer NOT NULL,
    source_markdown text NOT NULL,
    source_hash character varying(64) NOT NULL,
    parser_version character varying(32) NOT NULL,
    ast_json json,
    ast_source_hash character varying(64) DEFAULT NULL::character varying,
    parse_errors_json json,
    parse_status character varying(32) NOT NULL
);


--
-- Name: COLUMN world_entity_revision.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_entity_revision.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_entity_revision.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_entity_revision.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_entity_revision_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_entity_revision_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_entity_revision_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_entity_revision_id_seq OWNED BY public.world_entity_revision.id;


--
-- Name: world_event; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_event (
    id integer NOT NULL,
    location_id integer,
    start_at timestamp(0) without time zone NOT NULL,
    end_at timestamp(0) without time zone NOT NULL,
    summary text
);


--
-- Name: COLUMN world_event.start_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_event.start_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_event.end_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_event.end_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_event_participation; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_event_participation (
    id integer NOT NULL,
    event_id integer NOT NULL,
    participant_entity_id integer NOT NULL,
    source_revision_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    role character varying(100) NOT NULL,
    revealed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    description text
);


--
-- Name: COLUMN world_event_participation.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_event_participation.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_event_participation.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_event_participation.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_event_participation.revealed_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_event_participation.revealed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_event_participation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_event_participation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_event_participation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_event_participation_id_seq OWNED BY public.world_event_participation.id;


--
-- Name: world_fact; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_fact (
    id integer NOT NULL,
    subject_entity_id integer NOT NULL,
    source_revision_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    fact_type character varying(100) NOT NULL,
    value_text text NOT NULL,
    revealed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


--
-- Name: COLUMN world_fact.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_fact.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_fact.updated_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_fact.updated_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN world_fact.revealed_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.world_fact.revealed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: world_fact_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_fact_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_fact_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_fact_id_seq OWNED BY public.world_fact.id;


--
-- Name: world_faction; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_faction (
    id integer NOT NULL,
    faction_name character varying(100) NOT NULL
);


--
-- Name: world_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.world_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: world_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.world_id_seq OWNED BY public.world.id;


--
-- Name: world_item; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_item (
    id integer NOT NULL
);


--
-- Name: world_location; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_location (
    id integer NOT NULL,
    location_type character varying(100) DEFAULT NULL::character varying
);


--
-- Name: world_person; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.world_person (
    id integer NOT NULL,
    species character varying(100) DEFAULT NULL::character varying,
    gender character varying(100) DEFAULT NULL::character varying
);


--
-- Name: abuse_report id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.abuse_report ALTER COLUMN id SET DEFAULT nextval('public.abuse_report_id_seq'::regclass);


--
-- Name: blocked_email_address id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_email_address ALTER COLUMN id SET DEFAULT nextval('public.blocked_email_address_id_seq'::regclass);


--
-- Name: bug_report id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bug_report ALTER COLUMN id SET DEFAULT nextval('public.bug_report_id_seq'::regclass);


--
-- Name: campaign id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign ALTER COLUMN id SET DEFAULT nextval('public.campaign_id_seq'::regclass);


--
-- Name: campaign_membership id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign_membership ALTER COLUMN id SET DEFAULT nextval('public.campaign_membership_id_seq'::regclass);


--
-- Name: casebook id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook ALTER COLUMN id SET DEFAULT nextval('public.casebook_id_seq'::regclass);


--
-- Name: casebook_subject id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject ALTER COLUMN id SET DEFAULT nextval('public.casebook_subject_id_seq'::regclass);


--
-- Name: casebook_subject_clue id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue ALTER COLUMN id SET DEFAULT nextval('public.casebook_subject_clue_id_seq'::regclass);


--
-- Name: casebook_subject_clue_type id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue_type ALTER COLUMN id SET DEFAULT nextval('public.casebook_subject_clue_type_id_seq'::regclass);


--
-- Name: casebook_subject_note id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_note ALTER COLUMN id SET DEFAULT nextval('public.casebook_subject_note_id_seq'::regclass);


--
-- Name: casebook_subject_type id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_type ALTER COLUMN id SET DEFAULT nextval('public.casebook_subject_type_id_seq'::regclass);


--
-- Name: event id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.event ALTER COLUMN id SET DEFAULT nextval('public.event_id_seq'::regclass);


--
-- Name: feature_switcher id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_switcher ALTER COLUMN id SET DEFAULT nextval('public.feature_switcher_id_seq'::regclass);


--
-- Name: game id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game ALTER COLUMN id SET DEFAULT nextval('public.game_id_seq'::regclass);


--
-- Name: game_invitation id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_invitation ALTER COLUMN id SET DEFAULT nextval('public.game_invitation_id_seq'::regclass);


--
-- Name: game_session id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_session ALTER COLUMN id SET DEFAULT nextval('public.game_session_id_seq'::regclass);


--
-- Name: image_type_format_map id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.image_type_format_map ALTER COLUMN id SET DEFAULT nextval('public.image_type_format_map_id_seq'::regclass);


--
-- Name: managed_file id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_file ALTER COLUMN id SET DEFAULT nextval('public.managed_file_id_seq'::regclass);


--
-- Name: managed_image id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image ALTER COLUMN id SET DEFAULT nextval('public.managed_image_id_seq'::regclass);


--
-- Name: managed_image_derivative id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative ALTER COLUMN id SET DEFAULT nextval('public.managed_image_derivative_id_seq'::regclass);


--
-- Name: managed_image_derivative_format id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative_format ALTER COLUMN id SET DEFAULT nextval('public.managed_image_derivative_format_id_seq'::regclass);


--
-- Name: order_charge_line id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_charge_line ALTER COLUMN id SET DEFAULT nextval('public.order_charge_line_id_seq'::regclass);


--
-- Name: order_product_line id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_product_line ALTER COLUMN id SET DEFAULT nextval('public.order_product_line_id_seq'::regclass);


--
-- Name: order_tax id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_tax ALTER COLUMN id SET DEFAULT nextval('public.order_tax_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: organisation id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation ALTER COLUMN id SET DEFAULT nextval('public.organisation_id_seq'::regclass);


--
-- Name: payment id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment ALTER COLUMN id SET DEFAULT nextval('public.payment_id_seq'::regclass);


--
-- Name: payment_receipt id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment_receipt ALTER COLUMN id SET DEFAULT nextval('public.payment_receipt_id_seq'::regclass);


--
-- Name: permission id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission ALTER COLUMN id SET DEFAULT nextval('public.permission_id_seq'::regclass);


--
-- Name: play_event id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.play_event ALTER COLUMN id SET DEFAULT nextval('public.play_event_id_seq'::regclass);


--
-- Name: postal_address id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.postal_address ALTER COLUMN id SET DEFAULT nextval('public.postal_address_id_seq'::regclass);


--
-- Name: product id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product ALTER COLUMN id SET DEFAULT nextval('public.product_id_seq'::regclass);


--
-- Name: product_variant id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant ALTER COLUMN id SET DEFAULT nextval('public.product_variant_id_seq'::regclass);


--
-- Name: product_variant_stock id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant_stock ALTER COLUMN id SET DEFAULT nextval('public.product_variant_stock_id_seq'::regclass);


--
-- Name: puzzle_category id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.puzzle_category ALTER COLUMN id SET DEFAULT nextval('public.puzzle_category_id_seq'::regclass);


--
-- Name: puzzle_instance id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.puzzle_instance ALTER COLUMN id SET DEFAULT nextval('public.puzzle_instance_id_seq'::regclass);


--
-- Name: quotation id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.quotation ALTER COLUMN id SET DEFAULT nextval('public.quotation_id_seq'::regclass);


--
-- Name: reset_password_request id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reset_password_request ALTER COLUMN id SET DEFAULT nextval('public.reset_password_request_id_seq'::regclass);


--
-- Name: role id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role ALTER COLUMN id SET DEFAULT nextval('public.role_id_seq'::regclass);


--
-- Name: ticket id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket ALTER COLUMN id SET DEFAULT nextval('public.ticket_id_seq'::regclass);


--
-- Name: ticket_type id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type ALTER COLUMN id SET DEFAULT nextval('public.ticket_type_id_seq'::regclass);


--
-- Name: ticket_type_product_variant_map id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type_product_variant_map ALTER COLUMN id SET DEFAULT nextval('public.ticket_type_product_variant_map_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- Name: user_access_token id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_access_token ALTER COLUMN id SET DEFAULT nextval('public.user_access_token_id_seq'::regclass);


--
-- Name: user_account_type id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_account_type ALTER COLUMN id SET DEFAULT nextval('public.user_account_type_id_seq'::regclass);


--
-- Name: user_block id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_block ALTER COLUMN id SET DEFAULT nextval('public.user_block_id_seq'::regclass);


--
-- Name: user_cart id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart ALTER COLUMN id SET DEFAULT nextval('public.user_cart_id_seq'::regclass);


--
-- Name: user_cart_charge id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_charge ALTER COLUMN id SET DEFAULT nextval('public.user_cart_charge_id_seq'::regclass);


--
-- Name: user_cart_product_line id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_product_line ALTER COLUMN id SET DEFAULT nextval('public.user_cart_product_line_id_seq'::regclass);


--
-- Name: user_feat id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_feat ALTER COLUMN id SET DEFAULT nextval('public.user_feat_id_seq'::regclass);


--
-- Name: world id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world ALTER COLUMN id SET DEFAULT nextval('public.world_id_seq'::regclass);


--
-- Name: world_calendar_day_of_week id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_day_of_week ALTER COLUMN id SET DEFAULT nextval('public.world_calendar_day_of_week_id_seq'::regclass);


--
-- Name: world_calendar_month id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_month ALTER COLUMN id SET DEFAULT nextval('public.world_calendar_month_id_seq'::regclass);


--
-- Name: world_calendar_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_settings ALTER COLUMN id SET DEFAULT nextval('public.world_calendar_settings_id_seq'::regclass);


--
-- Name: world_edge id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_edge ALTER COLUMN id SET DEFAULT nextval('public.world_edge_id_seq'::regclass);


--
-- Name: world_entity id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity ALTER COLUMN id SET DEFAULT nextval('public.world_entity_id_seq'::regclass);


--
-- Name: world_entity_revision id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity_revision ALTER COLUMN id SET DEFAULT nextval('public.world_entity_revision_id_seq'::regclass);


--
-- Name: world_event_participation id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event_participation ALTER COLUMN id SET DEFAULT nextval('public.world_event_participation_id_seq'::regclass);


--
-- Name: world_fact id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_fact ALTER COLUMN id SET DEFAULT nextval('public.world_fact_id_seq'::regclass);


--
-- Name: abuse_report abuse_report_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.abuse_report
    ADD CONSTRAINT abuse_report_pkey PRIMARY KEY (id);


--
-- Name: blocked_email_address blocked_email_address_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_email_address
    ADD CONSTRAINT blocked_email_address_pkey PRIMARY KEY (id);


--
-- Name: bug_report bug_report_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bug_report
    ADD CONSTRAINT bug_report_pkey PRIMARY KEY (id);


--
-- Name: campaign_membership campaign_membership_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign_membership
    ADD CONSTRAINT campaign_membership_pkey PRIMARY KEY (id);


--
-- Name: campaign campaign_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign
    ADD CONSTRAINT campaign_pkey PRIMARY KEY (id);


--
-- Name: casebook casebook_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook
    ADD CONSTRAINT casebook_pkey PRIMARY KEY (id);


--
-- Name: casebook_subject_clue casebook_subject_clue_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue
    ADD CONSTRAINT casebook_subject_clue_pkey PRIMARY KEY (id);


--
-- Name: casebook_subject_clue_type casebook_subject_clue_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue_type
    ADD CONSTRAINT casebook_subject_clue_type_pkey PRIMARY KEY (id);


--
-- Name: casebook_subject_note casebook_subject_note_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_note
    ADD CONSTRAINT casebook_subject_note_pkey PRIMARY KEY (id);


--
-- Name: casebook_subject casebook_subject_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject
    ADD CONSTRAINT casebook_subject_pkey PRIMARY KEY (id);


--
-- Name: casebook_subject_type casebook_subject_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_type
    ADD CONSTRAINT casebook_subject_type_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: event event_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.event
    ADD CONSTRAINT event_pkey PRIMARY KEY (id);


--
-- Name: feature_switcher feature_switcher_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_switcher
    ADD CONSTRAINT feature_switcher_pkey PRIMARY KEY (id);


--
-- Name: game_invitation game_invitation_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_invitation
    ADD CONSTRAINT game_invitation_pkey PRIMARY KEY (id);


--
-- Name: game game_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game
    ADD CONSTRAINT game_pkey PRIMARY KEY (id);


--
-- Name: game_session game_session_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_session
    ADD CONSTRAINT game_session_pkey PRIMARY KEY (id);


--
-- Name: game_user game_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_user
    ADD CONSTRAINT game_user_pkey PRIMARY KEY (game_id, user_id);


--
-- Name: image_type_format_map_derivative_format image_type_format_map_derivative_format_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.image_type_format_map_derivative_format
    ADD CONSTRAINT image_type_format_map_derivative_format_pkey PRIMARY KEY (image_type_format_map_id, managed_image_derivative_format_id);


--
-- Name: image_type_format_map image_type_format_map_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.image_type_format_map
    ADD CONSTRAINT image_type_format_map_pkey PRIMARY KEY (id);


--
-- Name: managed_file managed_file_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_file
    ADD CONSTRAINT managed_file_pkey PRIMARY KEY (id);


--
-- Name: managed_image_derivative_format managed_image_derivative_format_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative_format
    ADD CONSTRAINT managed_image_derivative_format_pkey PRIMARY KEY (id);


--
-- Name: managed_image_derivative managed_image_derivative_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative
    ADD CONSTRAINT managed_image_derivative_pkey PRIMARY KEY (id);


--
-- Name: managed_image managed_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image
    ADD CONSTRAINT managed_image_pkey PRIMARY KEY (id);


--
-- Name: order_charge_line order_charge_line_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_charge_line
    ADD CONSTRAINT order_charge_line_pkey PRIMARY KEY (id);


--
-- Name: order_product_line order_product_line_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_product_line
    ADD CONSTRAINT order_product_line_pkey PRIMARY KEY (id);


--
-- Name: order_tax order_tax_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_tax
    ADD CONSTRAINT order_tax_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: organisation organisation_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation
    ADD CONSTRAINT organisation_pkey PRIMARY KEY (id);


--
-- Name: payment payment_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment
    ADD CONSTRAINT payment_pkey PRIMARY KEY (id);


--
-- Name: payment_receipt payment_receipt_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment_receipt
    ADD CONSTRAINT payment_receipt_pkey PRIMARY KEY (id);


--
-- Name: permission permission_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission
    ADD CONSTRAINT permission_pkey PRIMARY KEY (id);


--
-- Name: play_event play_event_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.play_event
    ADD CONSTRAINT play_event_pkey PRIMARY KEY (id);


--
-- Name: postal_address postal_address_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.postal_address
    ADD CONSTRAINT postal_address_pkey PRIMARY KEY (id);


--
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);


--
-- Name: product_variant_managed_image product_variant_managed_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant_managed_image
    ADD CONSTRAINT product_variant_managed_image_pkey PRIMARY KEY (product_variant_id, managed_image_id);


--
-- Name: product_variant product_variant_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant
    ADD CONSTRAINT product_variant_pkey PRIMARY KEY (id);


--
-- Name: product_variant_stock product_variant_stock_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant_stock
    ADD CONSTRAINT product_variant_stock_pkey PRIMARY KEY (id);


--
-- Name: puzzle_category puzzle_category_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.puzzle_category
    ADD CONSTRAINT puzzle_category_pkey PRIMARY KEY (id);


--
-- Name: puzzle_instance puzzle_instance_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.puzzle_instance
    ADD CONSTRAINT puzzle_instance_pkey PRIMARY KEY (id);


--
-- Name: quotation quotation_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.quotation
    ADD CONSTRAINT quotation_pkey PRIMARY KEY (id);


--
-- Name: reset_password_request reset_password_request_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reset_password_request
    ADD CONSTRAINT reset_password_request_pkey PRIMARY KEY (id);


--
-- Name: role_permission role_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_permission
    ADD CONSTRAINT role_permission_pkey PRIMARY KEY (role_id, permission_id);


--
-- Name: role role_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role
    ADD CONSTRAINT role_pkey PRIMARY KEY (id);


--
-- Name: ticket ticket_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket
    ADD CONSTRAINT ticket_pkey PRIMARY KEY (id);


--
-- Name: ticket_type ticket_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type
    ADD CONSTRAINT ticket_type_pkey PRIMARY KEY (id);


--
-- Name: ticket_type_product_variant_map ticket_type_product_variant_map_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type_product_variant_map
    ADD CONSTRAINT ticket_type_product_variant_map_pkey PRIMARY KEY (id);


--
-- Name: user_access_token user_access_token_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_access_token
    ADD CONSTRAINT user_access_token_pkey PRIMARY KEY (id);


--
-- Name: user_account_type user_account_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_account_type
    ADD CONSTRAINT user_account_type_pkey PRIMARY KEY (id);


--
-- Name: user_block user_block_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_block
    ADD CONSTRAINT user_block_pkey PRIMARY KEY (id);


--
-- Name: user_cart_charge user_cart_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_charge
    ADD CONSTRAINT user_cart_charge_pkey PRIMARY KEY (id);


--
-- Name: user_cart user_cart_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart
    ADD CONSTRAINT user_cart_pkey PRIMARY KEY (id);


--
-- Name: user_cart_product_line user_cart_product_line_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_product_line
    ADD CONSTRAINT user_cart_product_line_pkey PRIMARY KEY (id);


--
-- Name: user_feat user_feat_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_feat
    ADD CONSTRAINT user_feat_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: user_role user_role_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_role
    ADD CONSTRAINT user_role_pkey PRIMARY KEY (user_id, role_id);


--
-- Name: user_user_feat user_user_feat_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_user_feat
    ADD CONSTRAINT user_user_feat_pkey PRIMARY KEY (user_id, user_feat_id);


--
-- Name: user_world user_world_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_world
    ADD CONSTRAINT user_world_pkey PRIMARY KEY (user_id, world_id);


--
-- Name: world_calendar_day_of_week world_calendar_day_of_week_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_day_of_week
    ADD CONSTRAINT world_calendar_day_of_week_pkey PRIMARY KEY (id);


--
-- Name: world_calendar_month world_calendar_month_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_month
    ADD CONSTRAINT world_calendar_month_pkey PRIMARY KEY (id);


--
-- Name: world_calendar_settings world_calendar_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_settings
    ADD CONSTRAINT world_calendar_settings_pkey PRIMARY KEY (id);


--
-- Name: world_edge world_edge_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_edge
    ADD CONSTRAINT world_edge_pkey PRIMARY KEY (id);


--
-- Name: world_entity world_entity_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity
    ADD CONSTRAINT world_entity_pkey PRIMARY KEY (id);


--
-- Name: world_entity_revision world_entity_revision_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity_revision
    ADD CONSTRAINT world_entity_revision_pkey PRIMARY KEY (id);


--
-- Name: world_event_participation world_event_participation_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event_participation
    ADD CONSTRAINT world_event_participation_pkey PRIMARY KEY (id);


--
-- Name: world_event world_event_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event
    ADD CONSTRAINT world_event_pkey PRIMARY KEY (id);


--
-- Name: world_fact world_fact_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_fact
    ADD CONSTRAINT world_fact_pkey PRIMARY KEY (id);


--
-- Name: world_faction world_faction_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_faction
    ADD CONSTRAINT world_faction_pkey PRIMARY KEY (id);


--
-- Name: world_item world_item_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_item
    ADD CONSTRAINT world_item_pkey PRIMARY KEY (id);


--
-- Name: world_location world_location_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_location
    ADD CONSTRAINT world_location_pkey PRIMARY KEY (id);


--
-- Name: world_person world_person_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_person
    ADD CONSTRAINT world_person_pkey PRIMARY KEY (id);


--
-- Name: world world_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world
    ADD CONSTRAINT world_pkey PRIMARY KEY (id);


--
-- Name: campaign_membership_campaign_user_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX campaign_membership_campaign_user_unique ON public.campaign_membership USING btree (campaign_id, user_id);


--
-- Name: expr_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX expr_idx ON public.user_block USING btree (expiration_date);


--
-- Name: gm_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX gm_id ON public.game USING btree (games_master_id);


--
-- Name: idx_166c5c8fe48fd905; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_166c5c8fe48fd905 ON public.puzzle_instance USING btree (game_id);


--
-- Name: idx_1fc1a64fa76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1fc1a64fa76ed395 ON public.game_invitation USING btree (user_id);


--
-- Name: idx_1fc1a64fe48fd905; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1fc1a64fe48fd905 ON public.game_invitation USING btree (game_id);


--
-- Name: idx_2042c41fe48fd905; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_2042c41fe48fd905 ON public.casebook USING btree (game_id);


--
-- Name: idx_209aa41d1c914464; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_209aa41d1c914464 ON public.product_variant USING btree (product_variant_stock_id);


--
-- Name: idx_209aa41d4584665a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_209aa41d4584665a ON public.product_variant USING btree (product_id);


--
-- Name: idx_209aa41db03a8386; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_209aa41db03a8386 ON public.product_variant USING btree (created_by_id);


--
-- Name: idx_20fa50e6a76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_20fa50e6a76ed395 ON public.user_user_feat USING btree (user_id);


--
-- Name: idx_20fa50e6ac315356; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_20fa50e6ac315356 ON public.user_user_feat USING btree (user_feat_id);


--
-- Name: idx_21af999b713ff03d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_21af999b713ff03d ON public.abuse_report USING btree (reporting_user_id);


--
-- Name: idx_21af999be7566e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_21af999be7566e ON public.abuse_report USING btree (reported_user_id);


--
-- Name: idx_27392640cd67fc5d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_27392640cd67fc5d ON public.world_calendar_month USING btree (world_calendar_settings_id);


--
-- Name: idx_2de8c6a3a76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_2de8c6a3a76ed395 ON public.user_role USING btree (user_id);


--
-- Name: idx_2de8c6a3d60322ac; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_2de8c6a3d60322ac ON public.user_role USING btree (role_id);


--
-- Name: idx_3a77114361220ea6; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_3a77114361220ea6 ON public.world USING btree (creator_id);


--
-- Name: idx_3bae0aa79e6b1585; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_3bae0aa79e6b1585 ON public.event USING btree (organisation_id);


--
-- Name: idx_41e8f22da76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_41e8f22da76ed395 ON public.campaign_membership USING btree (user_id);


--
-- Name: idx_41e8f22df639f774; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_41e8f22df639f774 ON public.campaign_membership USING btree (campaign_id);


--
-- Name: idx_452d8056a80ef684; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_452d8056a80ef684 ON public.product_variant_managed_image USING btree (product_variant_id);


--
-- Name: idx_452d8056e37faca6; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_452d8056e37faca6 ON public.product_variant_managed_image USING btree (managed_image_id);


--
-- Name: idx_4586aafbe48fd905; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4586aafbe48fd905 ON public.game_session USING btree (game_id);


--
-- Name: idx_4ad0b8af4c3a3bb; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4ad0b8af4c3a3bb ON public.payment_receipt USING btree (payment_id);


--
-- Name: idx_583c023d8d9f6d38; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_583c023d8d9f6d38 ON public.order_product_line USING btree (order_id);


--
-- Name: idx_583c023da80ef684; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_583c023da80ef684 ON public.order_product_line USING btree (product_variant_id);


--
-- Name: idx_5f1975ef16678c77; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5f1975ef16678c77 ON public.managed_file USING btree (uploader_id);


--
-- Name: idx_6686ba65a76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6686ba65a76ed395 ON public.game_user USING btree (user_id);


--
-- Name: idx_6686ba65e48fd905; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6686ba65e48fd905 ON public.game_user USING btree (game_id);


--
-- Name: idx_6802f18243dfbfe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6802f18243dfbfe ON public.casebook_subject_note USING btree (casebook_subject_id);


--
-- Name: idx_6802f18b03a8386; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6802f18b03a8386 ON public.casebook_subject_note USING btree (created_by_id);


--
-- Name: idx_6d28840d8d9f6d38; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6d28840d8d9f6d38 ON public.payment USING btree (order_id);


--
-- Name: idx_6f7df886d60322ac; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6f7df886d60322ac ON public.role_permission USING btree (role_id);


--
-- Name: idx_6f7df886fed90cca; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_6f7df886fed90cca ON public.role_permission USING btree (permission_id);


--
-- Name: idx_7ce748aa76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7ce748aa76ed395 ON public.reset_password_request USING btree (user_id);


--
-- Name: idx_7da2ea43c8ab1102; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7da2ea43c8ab1102 ON public.image_type_format_map_derivative_format USING btree (image_type_format_map_id);


--
-- Name: idx_7da2ea43f73594cb; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7da2ea43f73594cb ON public.image_type_format_map_derivative_format USING btree (managed_image_derivative_format_id);


--
-- Name: idx_8265a6af8d9f6d38; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8265a6af8d9f6d38 ON public.order_charge_line USING btree (order_id);


--
-- Name: idx_8d93d6491ebbf454; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d93d6491ebbf454 ON public."user" USING btree (user_account_type_id);


--
-- Name: idx_8d93d6499e6b1585; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d93d6499e6b1585 ON public."user" USING btree (organisation_id);


--
-- Name: idx_901ab6a9a26889bc; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_901ab6a9a26889bc ON public.managed_image_derivative USING btree (parent_image_id);


--
-- Name: idx_901ab6a9b13d9776; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_901ab6a9b13d9776 ON public.managed_image_derivative USING btree (derivative_id);


--
-- Name: idx_901ab6a9d629f605; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_901ab6a9d629f605 ON public.managed_image_derivative USING btree (format_id);


--
-- Name: idx_911ec37b467f13ab; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_911ec37b467f13ab ON public.casebook_subject USING btree (casebook_id);


--
-- Name: idx_911ec37b68710875; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_911ec37b68710875 ON public.casebook_subject USING btree (casebook_subject_type_id);


--
-- Name: idx_9254f5a4cd67fc5d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9254f5a4cd67fc5d ON public.world_calendar_day_of_week USING btree (world_calendar_settings_id);


--
-- Name: idx_972efbf7a76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_972efbf7a76ed395 ON public.postal_address USING btree (user_id);


--
-- Name: idx_97a0ada3a76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_97a0ada3a76ed395 ON public.ticket USING btree (user_id);


--
-- Name: idx_97a0ada3c980d5c1; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_97a0ada3c980d5c1 ON public.ticket USING btree (ticket_type_id);


--
-- Name: idx_9e16392ba32ed756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e16392ba32ed756 ON public.world_entity USING btree (current_revision_id);


--
-- Name: idx_baced8a864d218e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_baced8a864d218e ON public.world_event USING btree (location_id);


--
-- Name: idx_be05421171f7e88b; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_be05421171f7e88b ON public.ticket_type USING btree (event_id);


--
-- Name: idx_ca71ed1ba76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ca71ed1ba76ed395 ON public.play_event USING btree (user_id);


--
-- Name: idx_cddaf5168d9f6d38; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_cddaf5168d9f6d38 ON public.order_tax USING btree (order_id);


--
-- Name: idx_d26cbf1542d8d3b5; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d26cbf1542d8d3b5 ON public.user_cart_product_line USING btree (user_cart_id);


--
-- Name: idx_d26cbf15a80ef684; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d26cbf15a80ef684 ON public.user_cart_product_line USING btree (product_variant_id);


--
-- Name: idx_d34a04ad9e6b1585; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d34a04ad9e6b1585 ON public.product USING btree (organisation_id);


--
-- Name: idx_d8b5ea1b8925311c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d8b5ea1b8925311c ON public.user_world USING btree (world_id);


--
-- Name: idx_d8b5ea1ba76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d8b5ea1ba76ed395 ON public.user_world USING btree (user_id);


--
-- Name: idx_e52ffdee79d0c0e4; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e52ffdee79d0c0e4 ON public.orders USING btree (billing_address_id);


--
-- Name: idx_e52ffdeea76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e52ffdeea76ed395 ON public.orders USING btree (user_id);


--
-- Name: idx_e52ffdeeebf23851; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e52ffdeeebf23851 ON public.orders USING btree (delivery_address_id);


--
-- Name: idx_e6e132b47e3c61f9; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e6e132b47e3c61f9 ON public.organisation USING btree (owner_id);


--
-- Name: idx_e8ec6d91b03a8386; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e8ec6d91b03a8386 ON public.managed_image USING btree (created_by_id);


--
-- Name: idx_efb778dd243dfbfe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_efb778dd243dfbfe ON public.casebook_subject_clue USING btree (casebook_subject_id);


--
-- Name: idx_efb778ddc54c8c93; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_efb778ddc54c8c93 ON public.casebook_subject_clue USING btree (type_id);


--
-- Name: idx_f2f40ffe42d8d3b5; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f2f40ffe42d8d3b5 ON public.user_cart_charge USING btree (user_cart_id);


--
-- Name: idx_f4c521b3c980d5c1; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f4c521b3c980d5c1 ON public.ticket_type_product_variant_map USING btree (ticket_type_id);


--
-- Name: idx_fae7da43b03a8386; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fae7da43b03a8386 ON public.world_entity_revision USING btree (created_by_id);


--
-- Name: idx_world_edge_from; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_edge_from ON public.world_edge USING btree (from_entity_id);


--
-- Name: idx_world_edge_source_revision; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_edge_source_revision ON public.world_edge USING btree (source_revision_id);


--
-- Name: idx_world_edge_to; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_edge_to ON public.world_edge USING btree (to_entity_id);


--
-- Name: idx_world_edge_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_edge_type ON public.world_edge USING btree (relationship_type);


--
-- Name: idx_world_entity_revision_entity; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_entity_revision_entity ON public.world_entity_revision USING btree (entity_id);


--
-- Name: idx_world_event_participation_event; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_event_participation_event ON public.world_event_participation USING btree (event_id);


--
-- Name: idx_world_event_participation_participant; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_event_participation_participant ON public.world_event_participation USING btree (participant_entity_id);


--
-- Name: idx_world_event_participation_source_revision; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_event_participation_source_revision ON public.world_event_participation USING btree (source_revision_id);


--
-- Name: idx_world_fact_source_revision; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_fact_source_revision ON public.world_fact USING btree (source_revision_id);


--
-- Name: idx_world_fact_subject; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_fact_subject ON public.world_fact USING btree (subject_entity_id);


--
-- Name: idx_world_fact_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_world_fact_type ON public.world_fact USING btree (fact_type);


--
-- Name: name_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX name_idx ON public.world USING btree (name);


--
-- Name: uniq_166c5c8f5f1f6053; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_166c5c8f5f1f6053 ON public.puzzle_instance USING btree (instance_code);


--
-- Name: uniq_17e2131f918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_17e2131f918020d9 ON public.casebook_subject_clue_type USING btree (handle);


--
-- Name: uniq_1f1512dd989d9b62; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_1f1512dd989d9b62 ON public.campaign USING btree (slug);


--
-- Name: uniq_1fc1a64fba14fccc; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_1fc1a64fba14fccc ON public.game_invitation USING btree (invitation_code);


--
-- Name: uniq_201add02918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_201add02918020d9 ON public.user_feat USING btree (handle);


--
-- Name: uniq_2042c41f989d9b62; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_2042c41f989d9b62 ON public.casebook USING btree (slug);


--
-- Name: uniq_209aa41df9038c4; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_209aa41df9038c4 ON public.product_variant USING btree (sku);


--
-- Name: uniq_232b318c268a15ce; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_232b318c268a15ce ON public.game USING btree (hero_image_file_id);


--
-- Name: uniq_232b318c989d9b62; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_232b318c989d9b62 ON public.game USING btree (slug);


--
-- Name: uniq_366ea16a5f37a13b; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_366ea16a5f37a13b ON public.user_access_token USING btree (token);


--
-- Name: uniq_3836d7e5c28e566c; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_3836d7e5c28e566c ON public.image_type_format_map USING btree (managed_image_type);


--
-- Name: uniq_3a771143989d9b62; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_3a771143989d9b62 ON public.world USING btree (slug);


--
-- Name: uniq_3bae0aa764d218e; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_3bae0aa764d218e ON public.event USING btree (location_id);


--
-- Name: uniq_3bae0aa78756d7c9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_3bae0aa78756d7c9 ON public.event USING btree (event_slug);


--
-- Name: uniq_3bae0aa798bb94c5; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_3bae0aa798bb94c5 ON public.event USING btree (hero_image_id);


--
-- Name: uniq_4586aafbb6094505; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_4586aafbb6094505 ON public.game_session USING btree (session_image_id);


--
-- Name: uniq_45e38225918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_45e38225918020d9 ON public.casebook_subject_type USING btree (handle);


--
-- Name: uniq_4ad0b8af8220c10d; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_4ad0b8af8220c10d ON public.payment_receipt USING btree (receipt_reference);


--
-- Name: uniq_5210f490918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_5210f490918020d9 ON public.puzzle_category USING btree (handle);


--
-- Name: uniq_57698a6a918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_57698a6a918020d9 ON public.role USING btree (handle);


--
-- Name: uniq_65495eda8925311c; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_65495eda8925311c ON public.world_calendar_settings USING btree (world_id);


--
-- Name: uniq_6d28840ded8a663b; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_6d28840ded8a663b ON public.payment USING btree (payment_reference);


--
-- Name: uniq_7122c47ea76ed395; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_7122c47ea76ed395 ON public.user_cart USING btree (user_id);


--
-- Name: uniq_8081ac81918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_8081ac81918020d9 ON public.user_account_type USING btree (handle);


--
-- Name: uniq_8d93d64986383b10; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_8d93d64986383b10 ON public."user" USING btree (avatar_id);


--
-- Name: uniq_8d93d649e7927c74; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON public."user" USING btree (email);


--
-- Name: uniq_911ec37b51412e32; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_911ec37b51412e32 ON public.casebook_subject USING btree (casebook_subject_image_file_id);


--
-- Name: uniq_97a0ada345ce25a0; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_97a0ada345ce25a0 ON public.ticket USING btree (ticket_code);


--
-- Name: uniq_9e16392b989d9b62; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_9e16392b989d9b62 ON public.world_entity USING btree (slug);


--
-- Name: uniq_ae399d0e918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_ae399d0e918020d9 ON public.managed_image_derivative_format USING btree (handle);


--
-- Name: uniq_d34a04ad65f6468e; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_d34a04ad65f6468e ON public.product USING btree (base_sku);


--
-- Name: uniq_e04992aa918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e04992aa918020d9 ON public.permission USING btree (handle);


--
-- Name: uniq_e52ffdee122432eb; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e52ffdee122432eb ON public.orders USING btree (order_reference);


--
-- Name: uniq_e6e132b4918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e6e132b4918020d9 ON public.organisation USING btree (handle);


--
-- Name: uniq_e6e132b49a0b6349; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e6e132b49a0b6349 ON public.organisation USING btree (organisation_logo_id);


--
-- Name: uniq_e6e132b4c6bdfeb; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e6e132b4c6bdfeb ON public.organisation USING btree (invoice_address_id);


--
-- Name: uniq_e6e132b4fd54954b; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e6e132b4fd54954b ON public.organisation USING btree (postal_address_id);


--
-- Name: uniq_e8ec6d917e8513bf; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_e8ec6d917e8513bf ON public.managed_image USING btree (source_image_id);


--
-- Name: uniq_entity_revision_number; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_entity_revision_number ON public.world_entity_revision USING btree (entity_id, revision_number);


--
-- Name: uniq_f4c521b3a80ef684; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_f4c521b3a80ef684 ON public.ticket_type_product_variant_map USING btree (product_variant_id);


--
-- Name: uniq_fda05d9a918020d9; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uniq_fda05d9a918020d9 ON public.feature_switcher USING btree (handle);


--
-- Name: user_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_idx ON public.user_block USING btree (user_id);


--
-- Name: username_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX username_idx ON public."user" USING btree (username);


--
-- Name: world_calendar_day_of_week_settings_abbreviation_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX world_calendar_day_of_week_settings_abbreviation_unique ON public.world_calendar_day_of_week USING btree (world_calendar_settings_id, abbreviation);


--
-- Name: world_calendar_day_of_week_settings_name_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX world_calendar_day_of_week_settings_name_unique ON public.world_calendar_day_of_week USING btree (world_calendar_settings_id, name);


--
-- Name: world_calendar_month_abbreviation_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX world_calendar_month_abbreviation_unique ON public.world_calendar_month USING btree (abbreviation);


--
-- Name: world_calendar_month_settings_delta_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX world_calendar_month_settings_delta_unique ON public.world_calendar_month USING btree (world_calendar_settings_id, delta);


--
-- Name: puzzle_instance fk_166c5c8fe48fd905; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.puzzle_instance
    ADD CONSTRAINT fk_166c5c8fe48fd905 FOREIGN KEY (game_id) REFERENCES public.game(id);


--
-- Name: game_invitation fk_1fc1a64fa76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_invitation
    ADD CONSTRAINT fk_1fc1a64fa76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: game_invitation fk_1fc1a64fe48fd905; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_invitation
    ADD CONSTRAINT fk_1fc1a64fe48fd905 FOREIGN KEY (game_id) REFERENCES public.game(id);


--
-- Name: casebook fk_2042c41fe48fd905; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook
    ADD CONSTRAINT fk_2042c41fe48fd905 FOREIGN KEY (game_id) REFERENCES public.game(id);


--
-- Name: product_variant fk_209aa41d1c914464; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant
    ADD CONSTRAINT fk_209aa41d1c914464 FOREIGN KEY (product_variant_stock_id) REFERENCES public.product_variant_stock(id);


--
-- Name: product_variant fk_209aa41d4584665a; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant
    ADD CONSTRAINT fk_209aa41d4584665a FOREIGN KEY (product_id) REFERENCES public.product(id);


--
-- Name: product_variant fk_209aa41db03a8386; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant
    ADD CONSTRAINT fk_209aa41db03a8386 FOREIGN KEY (created_by_id) REFERENCES public."user"(id);


--
-- Name: user_user_feat fk_20fa50e6a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_user_feat
    ADD CONSTRAINT fk_20fa50e6a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: user_user_feat fk_20fa50e6ac315356; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_user_feat
    ADD CONSTRAINT fk_20fa50e6ac315356 FOREIGN KEY (user_feat_id) REFERENCES public.user_feat(id) ON DELETE CASCADE;


--
-- Name: abuse_report fk_21af999b713ff03d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.abuse_report
    ADD CONSTRAINT fk_21af999b713ff03d FOREIGN KEY (reporting_user_id) REFERENCES public."user"(id);


--
-- Name: abuse_report fk_21af999be7566e; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.abuse_report
    ADD CONSTRAINT fk_21af999be7566e FOREIGN KEY (reported_user_id) REFERENCES public."user"(id);


--
-- Name: game fk_232b318c268a15ce; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game
    ADD CONSTRAINT fk_232b318c268a15ce FOREIGN KEY (hero_image_file_id) REFERENCES public.managed_image(id);


--
-- Name: game fk_232b318c3c75a0f1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game
    ADD CONSTRAINT fk_232b318c3c75a0f1 FOREIGN KEY (games_master_id) REFERENCES public."user"(id);


--
-- Name: world_calendar_month fk_27392640cd67fc5d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_month
    ADD CONSTRAINT fk_27392640cd67fc5d FOREIGN KEY (world_calendar_settings_id) REFERENCES public.world_calendar_settings(id);


--
-- Name: user_role fk_2de8c6a3a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_role
    ADD CONSTRAINT fk_2de8c6a3a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: user_role fk_2de8c6a3d60322ac; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_role
    ADD CONSTRAINT fk_2de8c6a3d60322ac FOREIGN KEY (role_id) REFERENCES public.role(id) ON DELETE CASCADE;


--
-- Name: world fk_3a77114361220ea6; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world
    ADD CONSTRAINT fk_3a77114361220ea6 FOREIGN KEY (creator_id) REFERENCES public."user"(id);


--
-- Name: event fk_3bae0aa764d218e; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.event
    ADD CONSTRAINT fk_3bae0aa764d218e FOREIGN KEY (location_id) REFERENCES public.postal_address(id);


--
-- Name: event fk_3bae0aa798bb94c5; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.event
    ADD CONSTRAINT fk_3bae0aa798bb94c5 FOREIGN KEY (hero_image_id) REFERENCES public.managed_image(id);


--
-- Name: event fk_3bae0aa79e6b1585; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.event
    ADD CONSTRAINT fk_3bae0aa79e6b1585 FOREIGN KEY (organisation_id) REFERENCES public.organisation(id);


--
-- Name: campaign_membership fk_41e8f22da76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign_membership
    ADD CONSTRAINT fk_41e8f22da76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: campaign_membership fk_41e8f22df639f774; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campaign_membership
    ADD CONSTRAINT fk_41e8f22df639f774 FOREIGN KEY (campaign_id) REFERENCES public.campaign(id) ON DELETE CASCADE;


--
-- Name: product_variant_managed_image fk_452d8056a80ef684; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant_managed_image
    ADD CONSTRAINT fk_452d8056a80ef684 FOREIGN KEY (product_variant_id) REFERENCES public.product_variant(id) ON DELETE CASCADE;


--
-- Name: product_variant_managed_image fk_452d8056e37faca6; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_variant_managed_image
    ADD CONSTRAINT fk_452d8056e37faca6 FOREIGN KEY (managed_image_id) REFERENCES public.managed_image(id) ON DELETE CASCADE;


--
-- Name: game_session fk_4586aafbb6094505; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_session
    ADD CONSTRAINT fk_4586aafbb6094505 FOREIGN KEY (session_image_id) REFERENCES public.managed_image(id);


--
-- Name: game_session fk_4586aafbe48fd905; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_session
    ADD CONSTRAINT fk_4586aafbe48fd905 FOREIGN KEY (game_id) REFERENCES public.game(id);


--
-- Name: payment_receipt fk_4ad0b8af4c3a3bb; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment_receipt
    ADD CONSTRAINT fk_4ad0b8af4c3a3bb FOREIGN KEY (payment_id) REFERENCES public.payment(id);


--
-- Name: order_product_line fk_583c023d8d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_product_line
    ADD CONSTRAINT fk_583c023d8d9f6d38 FOREIGN KEY (order_id) REFERENCES public.orders(id);


--
-- Name: order_product_line fk_583c023da80ef684; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_product_line
    ADD CONSTRAINT fk_583c023da80ef684 FOREIGN KEY (product_variant_id) REFERENCES public.product_variant(id);


--
-- Name: managed_file fk_5f1975ef16678c77; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_file
    ADD CONSTRAINT fk_5f1975ef16678c77 FOREIGN KEY (uploader_id) REFERENCES public."user"(id);


--
-- Name: user_block fk_61d96c7aa76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_block
    ADD CONSTRAINT fk_61d96c7aa76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: world_calendar_settings fk_65495eda8925311c; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_settings
    ADD CONSTRAINT fk_65495eda8925311c FOREIGN KEY (world_id) REFERENCES public.world(id);


--
-- Name: game_user fk_6686ba65a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_user
    ADD CONSTRAINT fk_6686ba65a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: game_user fk_6686ba65e48fd905; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.game_user
    ADD CONSTRAINT fk_6686ba65e48fd905 FOREIGN KEY (game_id) REFERENCES public.game(id) ON DELETE CASCADE;


--
-- Name: casebook_subject_note fk_6802f18243dfbfe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_note
    ADD CONSTRAINT fk_6802f18243dfbfe FOREIGN KEY (casebook_subject_id) REFERENCES public.casebook_subject(id);


--
-- Name: casebook_subject_note fk_6802f18b03a8386; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_note
    ADD CONSTRAINT fk_6802f18b03a8386 FOREIGN KEY (created_by_id) REFERENCES public."user"(id);


--
-- Name: world_faction fk_6c41a5c7bf396750; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_faction
    ADD CONSTRAINT fk_6c41a5c7bf396750 FOREIGN KEY (id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: payment fk_6d28840d8d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.payment
    ADD CONSTRAINT fk_6d28840d8d9f6d38 FOREIGN KEY (order_id) REFERENCES public.orders(id);


--
-- Name: role_permission fk_6f7df886d60322ac; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_permission
    ADD CONSTRAINT fk_6f7df886d60322ac FOREIGN KEY (role_id) REFERENCES public.role(id) ON DELETE CASCADE;


--
-- Name: role_permission fk_6f7df886fed90cca; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_permission
    ADD CONSTRAINT fk_6f7df886fed90cca FOREIGN KEY (permission_id) REFERENCES public.permission(id) ON DELETE CASCADE;


--
-- Name: user_cart fk_7122c47ea76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart
    ADD CONSTRAINT fk_7122c47ea76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: reset_password_request fk_7ce748aa76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reset_password_request
    ADD CONSTRAINT fk_7ce748aa76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: image_type_format_map_derivative_format fk_7da2ea43c8ab1102; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.image_type_format_map_derivative_format
    ADD CONSTRAINT fk_7da2ea43c8ab1102 FOREIGN KEY (image_type_format_map_id) REFERENCES public.image_type_format_map(id) ON DELETE CASCADE;


--
-- Name: image_type_format_map_derivative_format fk_7da2ea43f73594cb; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.image_type_format_map_derivative_format
    ADD CONSTRAINT fk_7da2ea43f73594cb FOREIGN KEY (managed_image_derivative_format_id) REFERENCES public.managed_image_derivative_format(id) ON DELETE CASCADE;


--
-- Name: order_charge_line fk_8265a6af8d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_charge_line
    ADD CONSTRAINT fk_8265a6af8d9f6d38 FOREIGN KEY (order_id) REFERENCES public.orders(id);


--
-- Name: user fk_8d93d6491ebbf454; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d6491ebbf454 FOREIGN KEY (user_account_type_id) REFERENCES public.user_account_type(id);


--
-- Name: user fk_8d93d64986383b10; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d64986383b10 FOREIGN KEY (avatar_id) REFERENCES public.managed_image(id) ON DELETE SET NULL;


--
-- Name: user fk_8d93d6499e6b1585; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d6499e6b1585 FOREIGN KEY (organisation_id) REFERENCES public.organisation(id);


--
-- Name: world_event_participation fk_8e19c76f21852c2f; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event_participation
    ADD CONSTRAINT fk_8e19c76f21852c2f FOREIGN KEY (source_revision_id) REFERENCES public.world_entity_revision(id) ON DELETE CASCADE;


--
-- Name: world_event_participation fk_8e19c76f4227b279; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event_participation
    ADD CONSTRAINT fk_8e19c76f4227b279 FOREIGN KEY (participant_entity_id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_event_participation fk_8e19c76f71f7e88b; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event_participation
    ADD CONSTRAINT fk_8e19c76f71f7e88b FOREIGN KEY (event_id) REFERENCES public.world_event(id) ON DELETE CASCADE;


--
-- Name: managed_image_derivative fk_901ab6a9a26889bc; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative
    ADD CONSTRAINT fk_901ab6a9a26889bc FOREIGN KEY (parent_image_id) REFERENCES public.managed_image(id);


--
-- Name: managed_image_derivative fk_901ab6a9b13d9776; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative
    ADD CONSTRAINT fk_901ab6a9b13d9776 FOREIGN KEY (derivative_id) REFERENCES public.managed_file(id);


--
-- Name: managed_image_derivative fk_901ab6a9d629f605; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image_derivative
    ADD CONSTRAINT fk_901ab6a9d629f605 FOREIGN KEY (format_id) REFERENCES public.managed_image_derivative_format(id);


--
-- Name: casebook_subject fk_911ec37b467f13ab; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject
    ADD CONSTRAINT fk_911ec37b467f13ab FOREIGN KEY (casebook_id) REFERENCES public.casebook(id);


--
-- Name: casebook_subject fk_911ec37b51412e32; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject
    ADD CONSTRAINT fk_911ec37b51412e32 FOREIGN KEY (casebook_subject_image_file_id) REFERENCES public.managed_image(id) ON DELETE SET NULL;


--
-- Name: casebook_subject fk_911ec37b68710875; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject
    ADD CONSTRAINT fk_911ec37b68710875 FOREIGN KEY (casebook_subject_type_id) REFERENCES public.casebook_subject_type(id);


--
-- Name: world_calendar_day_of_week fk_9254f5a4cd67fc5d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_calendar_day_of_week
    ADD CONSTRAINT fk_9254f5a4cd67fc5d FOREIGN KEY (world_calendar_settings_id) REFERENCES public.world_calendar_settings(id);


--
-- Name: postal_address fk_972efbf7a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.postal_address
    ADD CONSTRAINT fk_972efbf7a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: ticket fk_97a0ada3a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket
    ADD CONSTRAINT fk_97a0ada3a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: ticket fk_97a0ada3c980d5c1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket
    ADD CONSTRAINT fk_97a0ada3c980d5c1 FOREIGN KEY (ticket_type_id) REFERENCES public.ticket_type(id);


--
-- Name: world_entity fk_9e16392ba32ed756; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity
    ADD CONSTRAINT fk_9e16392ba32ed756 FOREIGN KEY (current_revision_id) REFERENCES public.world_entity_revision(id) ON DELETE SET NULL;


--
-- Name: world_person fk_a4e2ac35bf396750; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_person
    ADD CONSTRAINT fk_a4e2ac35bf396750 FOREIGN KEY (id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_location fk_ab7e08b2bf396750; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_location
    ADD CONSTRAINT fk_ab7e08b2bf396750 FOREIGN KEY (id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_edge fk_abc211b221852c2f; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_edge
    ADD CONSTRAINT fk_abc211b221852c2f FOREIGN KEY (source_revision_id) REFERENCES public.world_entity_revision(id) ON DELETE CASCADE;


--
-- Name: world_edge fk_abc211b291b9e8e7; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_edge
    ADD CONSTRAINT fk_abc211b291b9e8e7 FOREIGN KEY (from_entity_id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_edge fk_abc211b2b086aa2c; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_edge
    ADD CONSTRAINT fk_abc211b2b086aa2c FOREIGN KEY (to_entity_id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_fact fk_b160994121852c2f; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_fact
    ADD CONSTRAINT fk_b160994121852c2f FOREIGN KEY (source_revision_id) REFERENCES public.world_entity_revision(id) ON DELETE CASCADE;


--
-- Name: world_fact fk_b1609941c274e977; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_fact
    ADD CONSTRAINT fk_b1609941c274e977 FOREIGN KEY (subject_entity_id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_event fk_baced8a864d218e; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event
    ADD CONSTRAINT fk_baced8a864d218e FOREIGN KEY (location_id) REFERENCES public.world_location(id) ON DELETE SET NULL;


--
-- Name: world_event fk_baced8a8bf396750; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_event
    ADD CONSTRAINT fk_baced8a8bf396750 FOREIGN KEY (id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: ticket_type fk_be05421171f7e88b; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type
    ADD CONSTRAINT fk_be05421171f7e88b FOREIGN KEY (event_id) REFERENCES public.event(id);


--
-- Name: world_item fk_c1dfe7cabf396750; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_item
    ADD CONSTRAINT fk_c1dfe7cabf396750 FOREIGN KEY (id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: play_event fk_ca71ed1ba76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.play_event
    ADD CONSTRAINT fk_ca71ed1ba76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: order_tax fk_cddaf5168d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_tax
    ADD CONSTRAINT fk_cddaf5168d9f6d38 FOREIGN KEY (order_id) REFERENCES public.orders(id);


--
-- Name: user_cart_product_line fk_d26cbf1542d8d3b5; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_product_line
    ADD CONSTRAINT fk_d26cbf1542d8d3b5 FOREIGN KEY (user_cart_id) REFERENCES public.user_cart(id);


--
-- Name: user_cart_product_line fk_d26cbf15a80ef684; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_product_line
    ADD CONSTRAINT fk_d26cbf15a80ef684 FOREIGN KEY (product_variant_id) REFERENCES public.product_variant(id);


--
-- Name: product fk_d34a04ad9e6b1585; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT fk_d34a04ad9e6b1585 FOREIGN KEY (organisation_id) REFERENCES public.organisation(id);


--
-- Name: user_world fk_d8b5ea1b8925311c; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_world
    ADD CONSTRAINT fk_d8b5ea1b8925311c FOREIGN KEY (world_id) REFERENCES public.world(id) ON DELETE CASCADE;


--
-- Name: user_world fk_d8b5ea1ba76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_world
    ADD CONSTRAINT fk_d8b5ea1ba76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: orders fk_e52ffdee79d0c0e4; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_e52ffdee79d0c0e4 FOREIGN KEY (billing_address_id) REFERENCES public.postal_address(id);


--
-- Name: orders fk_e52ffdeea76ed395; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_e52ffdeea76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: orders fk_e52ffdeeebf23851; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_e52ffdeeebf23851 FOREIGN KEY (delivery_address_id) REFERENCES public.postal_address(id);


--
-- Name: organisation fk_e6e132b47e3c61f9; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation
    ADD CONSTRAINT fk_e6e132b47e3c61f9 FOREIGN KEY (owner_id) REFERENCES public."user"(id);


--
-- Name: organisation fk_e6e132b49a0b6349; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation
    ADD CONSTRAINT fk_e6e132b49a0b6349 FOREIGN KEY (organisation_logo_id) REFERENCES public.managed_image(id);


--
-- Name: organisation fk_e6e132b4c6bdfeb; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation
    ADD CONSTRAINT fk_e6e132b4c6bdfeb FOREIGN KEY (invoice_address_id) REFERENCES public.postal_address(id);


--
-- Name: organisation fk_e6e132b4fd54954b; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.organisation
    ADD CONSTRAINT fk_e6e132b4fd54954b FOREIGN KEY (postal_address_id) REFERENCES public.postal_address(id);


--
-- Name: managed_image fk_e8ec6d917e8513bf; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image
    ADD CONSTRAINT fk_e8ec6d917e8513bf FOREIGN KEY (source_image_id) REFERENCES public.managed_file(id);


--
-- Name: managed_image fk_e8ec6d91b03a8386; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.managed_image
    ADD CONSTRAINT fk_e8ec6d91b03a8386 FOREIGN KEY (created_by_id) REFERENCES public."user"(id);


--
-- Name: casebook_subject_clue fk_efb778dd243dfbfe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue
    ADD CONSTRAINT fk_efb778dd243dfbfe FOREIGN KEY (casebook_subject_id) REFERENCES public.casebook_subject(id);


--
-- Name: casebook_subject_clue fk_efb778ddc54c8c93; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.casebook_subject_clue
    ADD CONSTRAINT fk_efb778ddc54c8c93 FOREIGN KEY (type_id) REFERENCES public.casebook_subject_clue_type(id);


--
-- Name: user_cart_charge fk_f2f40ffe42d8d3b5; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cart_charge
    ADD CONSTRAINT fk_f2f40ffe42d8d3b5 FOREIGN KEY (user_cart_id) REFERENCES public.user_cart(id);


--
-- Name: ticket_type_product_variant_map fk_f4c521b3a80ef684; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type_product_variant_map
    ADD CONSTRAINT fk_f4c521b3a80ef684 FOREIGN KEY (product_variant_id) REFERENCES public.product_variant(id);


--
-- Name: ticket_type_product_variant_map fk_f4c521b3c980d5c1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ticket_type_product_variant_map
    ADD CONSTRAINT fk_f4c521b3c980d5c1 FOREIGN KEY (ticket_type_id) REFERENCES public.ticket_type(id);


--
-- Name: world_entity_revision fk_fae7da4381257d5d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity_revision
    ADD CONSTRAINT fk_fae7da4381257d5d FOREIGN KEY (entity_id) REFERENCES public.world_entity(id) ON DELETE CASCADE;


--
-- Name: world_entity_revision fk_fae7da43b03a8386; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.world_entity_revision
    ADD CONSTRAINT fk_fae7da43b03a8386 FOREIGN KEY (created_by_id) REFERENCES public."user"(id);


--
-- PostgreSQL database dump complete
--

\unrestrict fqxCLtxOUNjEgNgbRpJDDwR0Plb7WMclJQ6GZeOh5MKacjAtqST7kd8Gn2bOfTH
