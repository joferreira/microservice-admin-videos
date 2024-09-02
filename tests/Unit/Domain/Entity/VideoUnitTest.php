<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\{
    Rating,
    MediaStatus
};
use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\{
    Image,
    Media
};
use Core\Domain\ValueObject\Uuid as UuidValueObject;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class VideoUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string) Uuid::uuid4();
        $entity = new Video(
            id: new UuidValueObject($uuid),
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            published: true,
            createdAt: new DateTime(date('Y-m-d H:i:s')),
        );

        $this->assertEquals($uuid, $entity->id());
        $this->assertEquals('New title', $entity->title);
        $this->assertEquals('New description', $entity->description);
        $this->assertEquals(2029, $entity->yearLaunched);
        $this->assertEquals(12, $entity->duration);
        $this->assertEquals(true, $entity->opened);
        $this->assertEquals(Rating::RATE10, $entity->rating);
        $this->assertEquals(true, $entity->published);
    }

    public function testIdAndCreatedAt()
    {
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $this->assertNotEmpty($entity->id());
        $this->assertNotEmpty($entity->createdAt());
    }

    public function testAddCategoryId()
    {
        $categoryId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $this->assertCount(0, $entity->categoriesId);
        $entity->addCategoryId(
            categoryId: $categoryId
        );
        $entity->addCategoryId(
            categoryId: $categoryId
        );
        $this->assertCount(2, $entity->categoriesId);
    }

    public function testRemoveCategoryId()
    {
        $categoryId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $entity->addCategoryId(
            categoryId: $categoryId
        );
        $entity->addCategoryId(
            categoryId: 'uuid'
        );
        $this->assertCount(2, $entity->categoriesId);

        $entity->removeCategoryId(
            categoryId: $categoryId
        );
        $this->assertCount(1, $entity->categoriesId);
    }

    public function testAddGenreId()
    {
        $genreId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $this->assertCount(0, $entity->genresId);
        $entity->addGenre(
            genreId: $genreId
        );
        $entity->addGenre(
            genreId: $genreId
        );
        $this->assertCount(2, $entity->genresId);
    }

    public function testRemoveGenreId()
    {
        $genreId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $entity->addGenre(
            genreId: $genreId
        );
        $entity->addGenre(
            genreId: 'uuid'
        );
        $this->assertCount(2, $entity->genresId);

        $entity->removeGenre(
            genreId: $genreId
        );
        $this->assertCount(1, $entity->genresId);
    }

    public function testAddCastMemberId()
    {
        $castMemberId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $this->assertCount(0, $entity->castMemberIds);
        $entity->addCastMember(
            castMemberId: $castMemberId
        );
        $entity->addCastMember(
            castMemberId: $castMemberId
        );
        $this->assertCount(2, $entity->castMemberIds);
    }

    public function testRemoveCastMemberId()
    {
        $castMemberId = (string) Uuid::uuid4();
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );

        $entity->addCastMember(
            castMemberId: $castMemberId
        );
        $entity->addCastMember(
            castMemberId: 'uuid'
        );
        $this->assertCount(2, $entity->castMemberIds);

        $entity->removeCastMember(
            castMemberId: $castMemberId
        );
        $this->assertCount(1, $entity->castMemberIds);
    }

    public function testValueObjectImage()
    {
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            thumbFile: new Image('sslsl/image-filmex.png'),
        );

        $this->assertNotNull($entity->thumbFile());
        $this->assertInstanceOf(Image::class, $entity->thumbFile());
        $this->assertEquals('sslsl/image-filmex.png', $entity->thumbFile()->path());
    }
    public function testValueObjectImageToThumbHalf()
    {
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            thumbHalf: new Image('sslsl/image-filmex.png'),
        );

        $this->assertNotNull($entity->thumbHalf());
        $this->assertInstanceOf(Image::class, $entity->thumbHalf());
        $this->assertEquals('sslsl/image-filmex.png', $entity->thumbHalf()->path());
    }
    public function testValueObjectImageToBannerFile()
    {
        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            bannerFile: new Image('sslsl/image-filmex.png'),
        );

        $this->assertNotNull($entity->bannerFile());
        $this->assertInstanceOf(Image::class, $entity->bannerFile());
        $this->assertEquals('sslsl/image-filmex.png', $entity->bannerFile()->path());
    }

    public function testValueObjectMediaTrailer()
    {
        $trailerFile = new Media(
            filePath: 'path/trailer.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'path/encoded.extension',
        );

        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            trailerFile: $trailerFile,
        );

        $this->assertNotNull($entity->trailerFile());
        $this->assertInstanceOf(Media::class, $entity->trailerFile());
        $this->assertEquals('path/trailer.mp4', $entity->trailerFile()->filePath);
    }

    public function testValueObjectMediaVideoFile()
    {
        $videoFile = new Media(
            filePath: 'path/video.mp4',
            mediaStatus: MediaStatus::COMPLETE,
        );

        $entity = new Video(
            title: 'New title',
            description: 'New description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10,
            videoFile: $videoFile,
        );

        $this->assertNotNull($entity->videoFile());
        $this->assertInstanceOf(Media::class, $entity->videoFile());
        $this->assertEquals('path/video.mp4', $entity->videoFile()->filePath);
    }

    public function testException()
    {
        $this->expectException(NotificationException::class);

        new Video(
            title: 'ts',
            description: 'de',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE10
        );
    }
}